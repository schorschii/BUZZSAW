<?php

require_once('session.php');


if (isset($_GET['set_remoteplayer_session'])) {
	$_SESSION['remoteplayer'] = $_GET['set_remoteplayer_session'];
	exit();
}
if (isset($_POST['action_select']) && isset($_POST['player'])) {
	header('Location: remoteplayer.php?player=' . intval($_POST['player']));
	exit();
}
if (isset($_POST['action_remove']) && isset($_POST['player'])) {
	require_once('database.php');
	$sql = "DELETE FROM remote WHERE id = ?;";
	$statement = $mysqli->prepare($sql);
	$statement->bind_param('i', $_POST['player']);
	$statement->execute();
}
if (isset($_GET['set'])) {
	require_once('database.php');
	if (isset($_GET['state'])) {
		$sql = "UPDATE remote SET state = ? WHERE id = ?;";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param('ii', $_GET['state'], $_GET['set']);
		$statement->execute();
	}
	if (isset($_GET['track'])) {
		$sql = "UPDATE remote SET track_id = ? WHERE id = ?;";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param('ii', $_GET['track'], $_GET['set']);
		$statement->execute();
	}
	if (isset($_GET['position'])) {
		$sql = "UPDATE remote SET position = ? WHERE id = ?;";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param('ii', $_GET['position'], $_GET['set']);
		$statement->execute();
	}
	exit();
}
if (isset($_POST['create'])) {
	require_once('database.php');
	$sql = "INSERT INTO remote (title) VALUES (?);";
	$statement = $mysqli->prepare($sql);
	$statement->bind_param('s', $_POST['create']);
	$statement->execute();
}
if (isset($_GET['update'])) {
	require_once('database.php');
	$sql = "SELECT tr.title AS 'title', "
	     . "tr.id AS 'id', "
	     . "tr.path AS 'path', "
	     . "tr.track_number AS 'track_number', "
	     . "al.title AS 'album', "
	     . "al.id AS 'album_id', "
	     . "ar.title AS 'artist', "
	     . "rt.state AS 'state', "
	     . "rt.position AS 'position' "
	     . "FROM track tr "
	     . "INNER JOIN album al ON tr.album_id = al.id "
	     . "INNER JOIN artist ar ON tr.artist_id = ar.id "
	     . "INNER JOIN remote rt ON rt.track_id = tr.id "
	     . "WHERE rt.id = ?;";
	$statement = $mysqli->prepare($sql);
	$statement->bind_param("i", $_GET['update']);
	$statement->execute();
	$result = $statement->get_result();

	while($row = $result->fetch_object()) {
		echo $row->id . "\n";
		echo $row->path . "\n";
		echo $row->state . "\n";
		echo $row->title . "\n";
		echo $row->position . "\n";
		echo $row->album . "\n";
		echo $row->artist . "\n";
		break;
	}
	exit();
}


$currentURL = "";
$currentTitle = "Empty remote player";
$currentArtist = "";
$currentAlbum = "";
$currenttrack_number = "";
$currenttrack_id = "";
$currentAlbumId = -1;
$autoplay = true;
$currentHTMLTitle = "BUZZSAW audio server - no track selected";
if (isset($_GET['player'])) {
	$currenttrack_id = $_GET['player'];
}



// mobile device decisions
require_once('browser.php');
$visualize = false;
$detailsbox = true;
$playerelement = "video";
// decide if visualizations should be shown
if(getOS1() != "Android") $visualize = true;
// decide if details box on hover should be shown (bad on mobile devices)
// decide if media element should be audio instead of video (bad on mobile devices)
if(getOS1() == "Android" || getOS1() == "iPhone") {
	$detailsbox = false;
	$playerelement = "audio";
}

?>

<!DOCTYPE html>
<html>
<head>
	<title><?php echo $currentHTMLTitle; ?></title>
	<meta charset="utf-8"/>
	<script type="text/javascript" src="js/global.js"></script>
	<script type="text/javascript" src="js/player.js"></script>
	<script type="text/javascript" src="js/search.js"></script>
	<script type="text/javascript" src="js/remoteplayer.js"></script>
	<?php if($visualize) { ?>
	<script type="text/javascript" src="js/visualizer.js"></script>
	<link href="css/visualizer.css" rel="stylesheet">
	<?php } ?>
	<link href="css/global.css" rel="stylesheet">
	<link href="css/player.css" rel="stylesheet">
	<link href="css/library.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimal-ui">
	<link rel="icon" type="image/png" href="img/buzzsaw.png"><!-- tab & bookmarks icon -->
	<link rel="apple-touch-icon" href="img/buzzsaw.png"><!-- home shortcut icon apple -->
	<link rel="shortcut icon" href="img/buzzsaw.png"><!-- home shortcut icon android -->
	<link rel="apple-touch-startup-image" href="img/buzzsaw.png"><!-- splash screen -->
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-title" content="BUZZSAW"><!-- home shortcut title apple -->
	<!--<meta name="apple-mobile-web-app-capable" content="yes">--><!-- display web app as fullscreen apple -->
	<!--<meta name="mobile-web-app-capable" content="yes">--><!-- display web app as fullscreen android -->
</head>
<body>

	<<?php echo $playerelement; ?> id="mainPlayer" src="<?php echo $currentURL; ?>" ontimeupdate="getTime()" onplay="refreshPlayPauseButton();" onpause="refreshPlayPauseButton();" onloadedmetadata="setVideoStyle('');" <?php if ($autoplay) echo "autoplay='true'"; ?>>
		<!--<source id="mainPlayermp3" type="audio/mpeg">-->
		Your Browser does not support html audio elements :-/
	</<?php echo $playerelement; ?>>
	<input id="file-input" type="file" name="name" style="display: none;" onchange="alert('123'); obj('mainPlayermp3').src = URL.createObjectURL(document.getElementById('file-input').files[0]); alert('123');" />

	<div id="visualization">
		<canvas id="visualizationCanvas" width="1100" height="600"></canvas>
	</div>

	<div id="content">
		<div id="containerContainer">
			<?php if(!$visualize) { ?>
				<div id="vis_not_supported_hint">Audio visualization is only supported in Chrome, Chromium and Safari.</div>
			<?php } ?>
			<?php if($currenttrack_id == "") { ?>
				<h1>Please select a remote player to listen on</h1>
				<form method="POST">
					<?php
					require_once('database.php');
					$sql = "SELECT * FROM remote r ";
					$statement = $mysqli->prepare($sql);
					$statement->execute();
					$result = $statement->get_result();
					if($result->num_rows == 0) {
						echo "<div>You currently don't have any remote player</div>";
					} else {
						echo "<select name='player'>";
						while($row = $result->fetch_object()) {
							echo "<option value='" . $row->id . "'>" . $row->title . "</option>\n";
						}
						echo "</select> ";
						echo "<input type='submit' name='action_select' value='Select'> ";
						echo "<input type='submit' name='action_remove' value='Remove'> ";
					}
					?>
				</form>
				<form method="POST">
					<h1>Create a new remote player</h1>
					<input type="text" name="create">
					<input type="submit" value="Create">
				</form>
			<?php } ?>
		</div>
	</div>

	<div id="bottombar">
		<table id="controlbar">
			<tr>
				<td id="title">
					<div id="titleArtist">
						<?php echo $currentArtist; ?>
					</div>
					<div id="titleTitle">
						<?php echo $currentTitle; ?>
					</div>
				</td>

				<td style="width: 20%;"></td>

				<td id="time">
					<div id="albumimgperspective">
						<div id="albumimg">
						</div>
					</div>
					<div id="totalTime">
						<span id="timeHoursTotal"></span><!--
						--><span id="timeMinutesTotal">00</span><!--
						--><span id="timeSecondsTotal">00</span>
					</div>
					<div id="currentTime">
						<span id="timeHours"></span><!--
						--><span id="timeMinutes">00</span><!--
						--><span id="timeSeconds">00</span>
					</div>
				</td>
			</tr>
		</table>

		<div id="controls" class="tooltip" style="display:none;">
			<button id="btnPrev" class="roundButton roundButtonSmall" onClick="nextTrack(false);" onfocus="controlButton_onFocus();" onblur="controlButton_lostFocus();">prev</button>
			<button id="btnPlayPause" class="roundButton roundButtonBig" onClick="togglePlayPause();" onfocus="controlButton_onFocus();" onblur="controlButton_lostFocus();">&nbsp;</button>
			<button id="btnNext" class="roundButton roundButtonSmall" onClick="nextTrack(true);" onfocus="controlButton_onFocus();" onblur="controlButton_lostFocus();">next</button>
		</div>

		<div id="timeBar">
			<div id="timeBarCurrentLeftEdge"></div>
			<div id="timeBarBackground">
				<div id="timeBarCurrentIndicator" style="width: 0%;"></div>
			</div>
			<div id="timeBarCurrentRightEdge"></div>
		</div>
		<input type="range" id="timeBarCurrent" min="0" max="100" step="0.001" value="0" onchange="setTime();" onmousedown="refreshTimeBar=false;" onmouseup="refreshTimeBar=true;"></input>
	</div>

	<div id="menu">
		<button id="btnFullscreen" class="roundButton roundButtonSmall showOnHover" onclick="toggleFullscreen();" title="Toggle fullscreen mode">&nbsp;</button>
		<button id="btnCurrentPlaylist" class="roundButton roundButtonSmall showOnHover" onclick="window.location.href='remoteplayer.php';" title="Change player">&nbsp;</button>
		<button id="btnLogout" class="roundButton roundButtonSmall showOnHover" onclick="location = 'login.php?logout=1';" title="Log out">&nbsp;</button>
	</div>

	<?php if($currenttrack_id != "") { ?>
	<script>
	setInterval(function() {
		ajaxRequestRemote("remoteplayer.php?update=<?php echo $currenttrack_id; ?> ");
	}, 500);
	</script>
	<?php } ?>

</body>
</html>
