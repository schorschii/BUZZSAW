<?php
	require_once('session.php');
	require_once('global.php');
	session_write_close(); // don't block other scripts by locking the session file
?>

<!DOCTYPE html>
<html>
<head>
	<title>Scanning audio files</title>
	<meta charset="utf-8"/>
	<script type="text/javascript" src="js/global.js"></script>
	<link href="css/global.css" rel="stylesheet">
	<link href="css/scan.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

	<div id="loader">
		<img id="imgloader" src="img/buzzsaw.svg" />
		<br>
		<div>Scan in progress, please wait...</div>
	</div>

	<div id="scanprogress">
	<?php
	require_once('php/getID3/getid3/getid3.php');
	$getID3 = new getID3;

	require_once('database.php');

	// remove old data
	#if (!$mysqli->multi_query(file_get_contents("sql/clean.sql")))
	#	echo("<b>ERROR TRUNCATING TABLES:</b><br>" . $mysqli->error . "<br>");
	#clearStoredResults($mysqli);

	// remove all previous album cover thumbnails
	#$files = glob('./music_thumb/*');
	#foreach($files as $file){
	#	if(is_file($file))
	#	unlink($file);
	#}

	// find all music files inside the music directory
	$counter = 0; $counter_new = 0; $counter_update = 0;
	$fs_perm_warned = false;
	$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./music'));
	foreach ($it as $file) {
		if (isAudioFile($file)) {
			// check if file is already in database
			$track_id = -1;
			$track_cover = "";
			$sql = "SELECT tr.id AS 'id', tr.cover AS 'cover' "
				 . "FROM track tr "
				 . "WHERE tr.path = ?;";
			$statement = $mysqli->prepare($sql);
			$statement->bind_param('s', $file);
			$statement->execute();
			$result = $statement->get_result();
			while($row = $result->fetch_object()) {
				$track_id = $row->id;
				$track_cover = $row->cover;
			}

			// read id3 tags from file
			$FileInfo = $getID3->analyze($file);
			getid3_lib::CopyTagsToComments($FileInfo);

			// read artist
			$artist = "Unknown Artist";
			if (isset($FileInfo['comments_html']['artist'][0]))
				$artist = $FileInfo['comments_html']['artist'][0];

			// read album
			$album = "Unknown Album";
			if (isset($FileInfo['comments_html']['album'][0]))
				$album = $FileInfo['comments_html']['album'][0];

			// read title
			$title = "Unknown Title";
			if (isset($FileInfo['comments_html']['title'][0]))
				$title = $FileInfo['comments_html']['title'][0];

			// read track number
			$track_number = 0;
			if (isset($FileInfo['comments_html']['track_number'][0]))
				$track_number = $FileInfo['comments_html']['track_number'][0];
			if (strpos($track_number, '/') !== false) $track_number = explode('/', $track_number)[0];

			// read cover if available
			$cover = NULL;
			if (isset($FileInfo['comments']['picture'][0])) {
				if ($track_id != -1)
					$filename = $track_cover;
				else
					$filename = "music_thumb/" . $counter . ".jpg";

				if (file_put_contents($filename, $FileInfo['comments']['picture'][0]['data']) === false && $fs_perm_warned == false) {
					$fs_perm_warned = true;
					echo "<b>WARN:</b>&nbsp;Unable to write into ./music_thumb. Album covers will not be available.<br>";
				}
				$cover = $filename;
			}

			if ($track_id == -1) {
				// insert new track
				$sql = "CALL InsertTrack(?, ?, ?, ?, ?, ?);";
				$statement = $mysqli->prepare($sql);
				if (!$statement)
					echo "<b>PREPARE FAILED:</b>&nbsp;$sql<br>".$mysqli->error."<br>";
				if (!$statement->bind_param('ssssss', $title, $album, $artist, $file, $track_number, $cover))
					echo "<b>BIND FAILED:</b>&nbsp;$file<br>$sql<br>";
				if (!$statement->execute())
					echo "<b>EXEC FAILED:</b>&nbsp;$file<br>$sql<br>".$statement->error."<br>";
				$counter_new ++;
			} else {
				// update track
				$sql = "UPDATE track "
					 . "SET title = ?, track_number = ? "
					 . "WHERE id = ?;";
				$statement = $mysqli->prepare($sql);
				if (!$statement)
					echo "<b>PREPARE FAILED:</b>&nbsp;$sql<br>".$mysqli->error."<br>";
				if (!$statement->bind_param('sii', $title, $track_number, $track_id))
					echo "<b>BIND FAILED:</b>&nbsp;$file<br>$sql<br>";
				if (!$statement->execute())
					echo "<b>EXEC FAILED:</b>&nbsp;$file<br>$sql<br>".$statement->error."<br>";
				$counter_update ++;
			}

			flush(); ob_flush();
			$counter ++;
		}
	}

	echo "<br><b>Finished - inserted $counter_new new track(s), updated $counter_update track(s).</b><br>";
	echo "<a href='player.php' class='styled_link'>Open Player</a>";
	?>
	</div>

	<script>obj('imgloader').style.animation = "none";</script>

</body>
</html>
