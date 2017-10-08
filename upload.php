<?php
	require_once('session.php');
	require_once('global.php');

	$uploadform=true;
?>

<!DOCTYPE html>
<html>
<head>
	<title>Uploading audio files</title>
	<meta charset="utf-8"/>
	<script type="text/javascript" src="js/global.js"></script>
	<link href="css/global.css" rel="stylesheet">
	<link href="css/scan.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<?php
require_once('database.php');

$count = 0;
if (isset($_FILES['directory'])) {
	foreach ($_FILES['directory']['name'] as $i => $name) {
		$newFilePath = 'music/'.$name;
		if (move_uploaded_file($_FILES['directory']['tmp_name'][$i], $newFilePath)) {
			import($newFilePath);
		}
	}
}
if (isset($_FILES['uploads'])) {
	$total = count($_FILES['uploads']['name']);
	for($i=0; $i<$total; $i++) {
		$tmpFilePath = $_FILES['uploads']['tmp_name'][$i];
		if ($tmpFilePath != ""){
			$newFilePath = "music/" . $_FILES['uploads']['name'][$i];
			if(move_uploaded_file($tmpFilePath, $newFilePath)) {
				import($newFilePath);
			}
		}
	}
}
function import($file) {
	global $mysqli;
	global $uploadform; $uploadform = false;

	if (isAudioFile($file)) {
		global $count; $count++;

		require_once('php/getID3/getid3/getid3.php');
		$getID3 = new getID3;
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

		// set path
		$path = $file;

		// read cover if available
		$cover = NULL;
		if (isset($FileInfo['comments']['picture'][0])) {
			$filename = microtime();
			if (file_put_contents("music_thumb/$filename.jpg", $FileInfo['comments']['picture'][0]['data']) === false && $fs_perm_warned == false) {
				$fs_perm_warned = true;
				echo "<b>WARN:</b>&nbsp;Unable to write into ./music_thumb. Album covers will not be available.<br>";
			}
			$cover = "music_thumb/$filename.jpg";
		}

		// build insert sql query
		$sql = "CALL InsertTrack(?, ?, ?, ?, ?, ?);";

		// insert
		$statement = $mysqli->prepare($sql);
		if (!$statement)
			echo "<b>PREPARE FAILED:</b>&nbsp;$file<br>$sql<br>".$mysqli->error."<br>";
		if (!$statement->bind_param('ssssss', $title, $album, $artist, $path, $track_number, $cover))
			echo "<b>BIND FAILED:</b>&nbsp;$file<br>$sql<br>";
		if (!$statement->execute())
			echo "<b>EXEC FAILED:</b>&nbsp;$file<br>$sql<br>".$statement->error."<br>";

		flush(); ob_flush();
	}
}
?>

<?php if($uploadform == true) { ?>
<h1>Upload to the music server</h1>
<h2>Upload file(s)</h2>
<form method="POST" enctype="multipart/form-data">
	<input name="uploads[]" type="file" multiple="multiple" />
	<input type="submit" />
</form>
<h2>Upload folder</h2>
<form method="POST" enctype="multipart/form-data">
	<input type="file" name="directory[]" webkitdirectory directory multiple />
	<input type="submit" />
</form>
<?php } else { ?>
<h2>Imported <?php echo $count; ?> tracks.</h2>
<a class='styled_link' href='player.php'>Play</a>
<?php } ?>

</body>
</html>
