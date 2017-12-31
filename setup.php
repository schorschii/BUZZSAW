<?php

require_once('database.php');
require_once('global.php');


$showsetupbtn = false;
if(IsAlreadyEstablished($mysqli)) {
	$info = "Your BUZZSAW database is<br>already ready to <a href='login.php' class='styled_link'>use</a>.";
} else {
	$showsetupbtn = true;
	$info = "<b>Database setup ahead</b><br><div>Click the button below to <br>create the required tables.</div><br>";

	if(isset($_POST['action']) && $_POST['action'] == "setup") {
		if (!$mysqli->multi_query(file_get_contents("sql/clean.sql")))
			echo("<b>ERROR DROPPING TABLES:</b><br>" . $mysqli->error . "<br>");
		clearStoredResults($mysqli);
		if (!$mysqli->multi_query(file_get_contents("sql/tables.sql")))
			die("<b>ERROR CREATING TABLES:</b><br>" . $mysqli->error . "<br>");
		clearStoredResults($mysqli);
		if (!$mysqli->query(file_get_contents("sql/InsertUpdateTrack.sql")))
			die("<b>ERROR IMPORTING FUNCTION INSERTTRACK:</b><br>" . $mysqli->error . "<br>");
		clearStoredResults($mysqli);
		if (!$mysqli->query(file_get_contents("sql/PurgeAlbumArtist.sql")))
			die("<b>ERROR IMPORTING FUNCTION INSERTTRACK:</b><br>" . $mysqli->error . "<br>");
		clearStoredResults($mysqli);
		if (!$mysqli->query(file_get_contents("sql/MoveTrackInPlaylist.sql")))
			die("<b>ERROR IMPORTING FUNCTION MOVETRACKINPLAYLIST:</b><br>" . $mysqli->error . "<br>");

		$showsetupbtn = false;
		$info = "<b>Setup finished.</b><br>"
		      . "You can now <a href='login.php' class='styled_link'>log in</a> without<br>"
		      . "a password. After that, go to<br>"
		      . "<i>options</i> and scan for tracks.";
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>BUZZSAW - Setup</title>
	<meta charset="utf-8"/>
	<script type="text/javascript" src="js/global.js"></script>
	<link href="css/global.css" rel="stylesheet">
	<link href="css/login.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Montserrat+Subrayada|Open+Sans+Condensed:300" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimal-ui">
	<link rel="icon" type="image/png" href="img/buzzsaw.png"><!-- tab & bookmarks icon -->
</head>
<body>

	<div id="logincontainer">
		<img id="logo" src="img/buzzsaw.svg"></img>
		<form method="POST" action="setup.php">
			<h1>BUZZSAW</h1>
			<h2>audio server</h2>
			<div><?php echo $info; ?></div>
			<?php if($showsetupbtn) { ?>
			<input type="hidden" name="action" value="setup">
			<input type="submit" value="Set up database">
			<?php } ?>
		</form>
	</div>

</body>
</html>
