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
	$THUMB_DIR = "music_thumb";
	$MUSIC_DIR = MEDIAROOT;

	// remove old db entries and album cover thumbnails
	if(isset($_GET['rescan']) && $_GET['rescan'] == 1) {
		if (!$mysqli->multi_query(file_get_contents("sql/clean.sql")))
			die("<b>ERROR TRUNCATING TABLES:</b><br>" . $mysqli->error . "<br>");
		clearStoredResults($mysqli);

		$files = glob($THUMB_DIR.'/*');
		foreach($files as $file){
			if(is_file($file)) unlink($file);
		}
	}

	// find all music files inside the music directory
	$counter = 0;
	$fs_perm_warned = false;
	$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($MUSIC_DIR));
	foreach ($it as $file) {
		if (isAudioFile($file)) {
			// check if file is already in database
			$track_id = -1;
			$track_cover = "";
			$sql = "SELECT tr.id AS 'id', tr.cover AS 'cover' FROM track tr WHERE tr.path = ?";
			$statement = $mysqli->prepare($sql);
			$statement->bind_param('s', $file);
			if (!$statement->execute())
				echo("<b>EXEC FAILED:</b>&nbsp;$file<br>$sql<br>".$statement->error."<br>");
			$result = $statement->get_result();
			if (!$result)
				echo("<b>GET RESULT FAILED:</b>&nbsp;$file<br>$sql<br>".$statement->error."<br>");
			while($row = $result->fetch_object()) {
				$track_id = $row->id;
				$track_cover = $row->cover;
			}

			// read id3 tags from file
			$FileInfo = $getID3->analyze($file);
			getid3_lib::CopyTagsToComments($FileInfo);

			// get file length
			$filelength = 0;
			$filelength = filesize($file);

			// read playtime
			$playtime = 0;
			if (isset($FileInfo['playtime_seconds']))
				$playtime = $FileInfo['playtime_seconds'];

			// read genre
			$genre = null;
			if (isset($FileInfo['comments_html']['genre'][0]))
				$genre = $FileInfo['comments_html']['genre'][0];

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
			else
				$title = pathinfo($file)['filename'];

			// read track number
			$track_number = 0;
			if (isset($FileInfo['comments_html']['track_number'][0]))
				$track_number = $FileInfo['comments_html']['track_number'][0];
			if (strpos($track_number, '/') !== false) $track_number = explode('/', $track_number)[0];

			// read cover if available
			$cover = null;
			if (isset($FileInfo['comments']['picture'][0])
			&& $FileInfo['comments']['picture'][0] != "") {
				if ($track_id == -1 || $track_cover == "")
					$filename = $THUMB_DIR . "/" . findFreeImageNumber() . ".jpg";
				else
					$filename = $track_cover;

				if (file_put_contents($filename, $FileInfo['comments']['picture'][0]['data']) === false && $fs_perm_warned == false) {
					$fs_perm_warned = true;
					echo "<b>WARN:</b>&nbsp;Unable to write into cover thumbnail directory. Album covers will not be available.<br>";
				}
				$cover = $filename;
			}

			// call insert/update sql procedure
			$sql = "CALL InsertUpdateTrack(?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$statement = $mysqli->prepare($sql);
			if (!$statement)
				echo("<b>PREPARE FAILED:</b>&nbsp;".$mysqli->error."<br>");
			if (!$statement->bind_param('ssssssiis', $title, $album, $artist, $file, $track_number, $cover, $playtime, $filelength, $genre))
				echo("<b>BIND FAILED:</b>&nbsp;$file<br>");
			if (!$statement->execute())
				echo("<b>EXEC FAILED:</b>&nbsp;$file<br>".$statement->error."<br>");

			flush(); ob_flush();
			$counter ++;
		}
	}

	// clean removed tracks
	$sql = "SELECT * FROM track";
	$statement = $mysqli->prepare($sql);
	$statement->execute();
	$result = $statement->get_result();
	while($row = $result->fetch_object()) {
		if(!file_exists($row->path)) {
			$id = $row->id;
			$sql = "DELETE FROM track WHERE id = ?";
			$statement = $mysqli->prepare($sql);
			$statement->bind_param('i', $id);
			$statement->execute();
		}
	}

	// call cleanup script
	$sql = "CALL PurgeAlbumArtist";
	$statement = $mysqli->prepare($sql);
	if (!$statement)
		echo("<b>PREPARE FAILED:</b>&nbsp;$sql<br>".$mysqli->error."<br>");
	if (!$statement->execute())
		echo("<b>EXEC FAILED:</b>&nbsp;$file<br>$sql<br>".$statement->error."<br>");

	echo "<br><b>Finished - scanned $counter track(s).</b><br>";
	echo "<a href='player.php' class='styled_link'>Open Player</a>";
	?>
	</div>

	<script>obj('imgloader').style.animation = "none";</script>

</body>
</html>

<?php
// find next free number for image file
function findFreeImageNumber() {
	global $THUMB_DIR;
	$free = false;
	$img_free_counter = 0;
	while (!$free) {
		$img_free_counter ++;
		if (!file_exists($THUMB_DIR . "/" . $img_free_counter . ".jpg"))
			$free = true;
	}
	return $img_free_counter;
}
?>
