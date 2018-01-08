<?php

require_once('session.php');


$currentURL = "";
$currentTrackId = -1;
$currentTitle = "";
$currentArtist = "";
$currentAlbum = "";
$currenttrack_number = 0;
$currentAlbumId = -1;
$autoplay = true;
$currentHTMLTitle = "BUZZSAW";
$currentplaylist = "";
$currentcover = "";
$currentArtistId = -1;
$libparam_scrollto = "";
if (isset($_GET['track'])) {
	if(file_exists($_GET['track'])) {
		$currentURL = "music.php?track=".$_GET['track'];
		$currentTitle = basename($_GET['track']);
		$currentArtist = basename(dirname($_GET['track']));
		$currentAlbum = "";
	} else {
		require_once('database.php');
		$sql = "SELECT tr.title AS 'title', "
			 . "tr.id AS 'track_id', "
			 . "tr.path AS 'path', "
			 . "tr.track_number AS 'track_number', "
			 . "al.title AS 'album', "
			 . "al.id AS 'album_id', "
			 . "ar.title AS 'artist', "
			 . "ar.id AS 'artist_id', "
			 . "tr.cover AS 'cover' "
			 . "FROM track tr "
			 . "INNER JOIN album al ON tr.album_id = al.id "
			 . "INNER JOIN artist ar ON tr.artist_id = ar.id "
			 . "WHERE tr.id = ?;";
		$statement = $mysqli->prepare($sql);
		$statement->bind_param('i', $_GET['track']);
		$statement->execute();
		$result = $statement->get_result();

		while($row = $result->fetch_object()) {
			$currentURL = "music.php?track=".$_GET['track'];
			$currentTrackId = $row->track_id;
			$currentTitle = $row->title;
			$currentArtist = $row->artist;
			$currentAlbum = $row->album;
			$currentAlbumId = $row->album_id;
			$currentcover = $row->cover;
			$currentArtistId = $row->artist_id;
			$libparam_scrollto = $row->artist_id;
			break;
		}
	}

	$currentHTMLTitle = "$currentTitle - $currentArtist";


	if (isset($_GET['currentplaylist'])) {
		switch ($_GET['currentplaylist']) {
			case "dir":
				if (isset($_GET['track']) && file_exists($_GET['track'])) {
					require_once('global.php');
					$counter = 1;
					foreach(scandir(dirname($_GET['track'])) as $file) {
						if ($file == "." || $file == "..") continue;
						if (is_dir(dirname($_GET['track'])."/".$file)) continue;
						if (!isAudioFile(dirname($_GET['track'])."/".$file)) continue;
						$active = "";
						if ($file == basename($_GET['track'])) {
							$active ="active";
							$currenttrack_number = $counter;
						}
						$currentplaylist .= createCurrentPlaylistEntry($counter, basename($file), basename(dirname($_GET['track'])), " ", "music.php?track=".dirname($_GET['track'])."/".$file, $active, -1);
						$counter ++;
					}
				}
				break;

			case "album":
				require_once('database.php');
				$sql = "SELECT @curRow := @curRow + 1 AS 'rank', "
				     . "tr.title AS 'title', "
				     . "tr.path AS 'path', "
				     . "tr.track_number AS 'track_number', "
				     . "tr.id AS 'id', "
				     . "al.title AS 'album', "
				     . "ar.title AS 'artist' "
				     . "FROM track tr "
				     . "INNER JOIN album al ON tr.album_id = al.id "
				     . "INNER JOIN artist ar ON tr.artist_id = ar.id "
				     . "JOIN  (SELECT @curRow := 0) r "
				     . "WHERE al.id = $currentAlbumId "
				     . "ORDER BY tr.track_number";
				$statement = $mysqli->prepare($sql);
				$statement->execute();
				$result = $statement->get_result();

				while($row = $result->fetch_object()) {
					$active = "";
					if ($row->id == $_GET['track']) {
						$active ="active";
						$currenttrack_number = $row->rank;
					}
					$currentplaylist .= createCurrentPlaylistEntry($row->track_number, $row->title, $row->artist, $row->album, "music.php?track=".$row->id, $active, $row->id);
				}
				break;

			case "artist":
				require_once('database.php');
				$sql = "SELECT @curRow := @curRow + 1 AS 'rank', "
				     . "tr.title AS 'title', "
				     . "tr.path AS 'path', "
				     . "tr.track_number AS 'track_number', "
				     . "tr.id AS 'id', "
				     . "al.title AS 'album', "
				     . "ar.title AS 'artist' "
				     . "FROM track tr "
				     . "INNER JOIN album al ON tr.album_id = al.id "
				     . "INNER JOIN artist ar ON tr.artist_id = ar.id "
				     . "JOIN  (SELECT @curRow := 0) r "
				     . "WHERE ar.id = $currentArtistId "
				     . "ORDER BY al.id, tr.track_number, tr.title ASC";
				$statement = $mysqli->prepare($sql);
				$statement->execute();
				$result = $statement->get_result();

				while($row = $result->fetch_object()) {
					$active = "";
					if ($row->id == $_GET['track']) {
						$active ="active";
						$currenttrack_number = $row->rank;
					}
					$currentplaylist .= createCurrentPlaylistEntry($row->track_number, $row->title, $row->artist, $row->album, "music.php?track=".$row->id, $active, $row->id);
				}
				break;

			case "playlist":
				$sql = "SELECT @curRow := @curRow + 1 AS 'rank', "
				     . "tr.title AS 'title', "
				     . "tr.path AS 'path', "
				     . "tr.track_number AS 'track_number', "
				     . "tr.id AS 'id', "
				     . "al.title AS 'album', "
				     . "ar.title AS 'artist' "
				     . "FROM playlist_track pt "
				     . "INNER JOIN track tr ON tr.id = pt.track_id "
				     . "INNER JOIN album al ON tr.album_id = al.id "
				     . "INNER JOIN artist ar ON tr.artist_id = ar.id "
				     . "JOIN  (SELECT @curRow := 0) r "
				     . "WHERE pt.playlist_id = ? "
				     . "ORDER BY pt.sequence";
				$statement = $mysqli->prepare($sql);
				$statement->bind_param('i', $_GET['playlist']);
				$statement->execute();
				$result = $statement->get_result();

				while($row = $result->fetch_object()) {
					$active = "";
					if ($row->id == $_GET['track']) {
						$active ="active";
						$currenttrack_number = $row->rank;
					}
					$currentplaylist .= createCurrentPlaylistEntry($row->rank, $row->title, $row->artist, $row->album, "music.php?track=".$row->id, $active, $row->id);
				}
				break;

			default:
		}
	}
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


function createCurrentPlaylistEntry($track_number, $title, $artist, $album, $path, $active, $track_id) {
	return "\t<li class='tracklisting'>\n"
	     . "\t\t<a class='track $active' href=\"" . $path . "\" titleTitle=\"$title\" titleArtist=\"$artist\" titleAlbum=\"$album\" titleID=\"$track_id\">\n"
	     . "\t\t\t<span class='track_number'>" . $track_number . "</span> " . $title . " - " . $artist
	     . "\t\t</a>\n"
	     . "\t</li>\n";
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
	<script type="text/javascript" src="js/library.js"></script>
	<?php if($visualize) { ?>
	<script type="text/javascript" src="js/visualizer.js"></script>
	<link href="css/visualizer.css" rel="stylesheet">
	<?php } ?>
	<?php if(getOS1() == "iPhone") { ?>
	<script type="text/javascript" src="js/ios-webapp-fix.js"></script>
	<?php } ?>
	<link href="css/global.css" rel="stylesheet">
	<link href="css/player.css" rel="stylesheet">
	<link href="css/library.css" rel="stylesheet">
	<link href="css/tooltip.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimal-ui">
	<link rel="icon" type="image/png" href="img/buzzsaw.png"><!-- tab & bookmarks icon -->
	<link rel="apple-touch-icon" href="img/buzzsaw.png"><!-- home shortcut icon apple -->
	<link rel="shortcut icon" href="img/buzzsaw.png"><!-- home shortcut icon android -->
	<link rel="apple-touch-startup-image" href="img/buzzsaw.png"><!-- splash screen -->
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-title" content="BUZZSAW"><!-- home shortcut title apple -->
	<!--<meta name="apple-mobile-web-app-capable" content="yes">--><!-- display web app as fullscreen apple -->
	<meta name="mobile-web-app-capable" content="yes"><!-- display web app as fullscreen android -->
	<!-- BUZZSAW audio server (c) Georg Sieber 2017 -->
</head>
<body>

	<noscript>
		JavaScript is not enabled in your browser. You have to enable JavaScript for BUZZSAW to work.
	</noscript>

	<div id="logocontainer">
		<img id="logo" src="img/buzzsaw.svg">
	</div>

	<div id="visualization">
		<canvas id="visualizationCanvas" width="1100" height="600"></canvas>
	</div>

	<<?php echo $playerelement; ?> id="mainPlayer" src="<?php echo $currentURL; ?>" ontimeupdate="getTime()" onplay="refreshPlayPauseButton();" onloadedmetadata="setVideoStyle('');" onpause="refreshPlayPauseButton();" <?php if ($autoplay) echo "autoplay='true'"; ?>>
		Your Browser does not support html audio elements :-/
	</<?php echo $playerelement; ?>>
	<?php if($currentTrackId!=-1) echo "<script>currentTrackId=$currentTrackId</script>"; ?>
	<?php if(isset($_SESSION['remoteplayer'])&&$_SESSION['remoteplayer']!=-1&&$_SESSION['remoteplayer']!="") echo "<script>remotePlayerId=".$_SESSION['remoteplayer'].";sendAllRemotePlayerParameters();fadeAudioOut(\"mainPlayer\", false);</script>"; ?>
	<input id="file-input" type="file" name="name" style="display: none;" onchange="alert('123'); obj('mainPlayermp3').src = URL.createObjectURL(document.getElementById('file-input').files[0]); alert('123');" />

	<div id="dummy" style="display: none;"></div>

	<div id="contentbg"></div>

	<div id="content" style="display: none;">
	<?php if(!$visualize) { ?>
		<div id="vis_not_supported_hint">Audio visualization is not supported on mobile devices.</div>
	<?php } ?>
	</div>

	<div id="currentPlaylist" style="display:none;">
		<div id="currentPlaylistContainer">
			<iframe id="download_frame" style="display:none;"></iframe>
			<table class="inputtbl">
				<tr>
					<th><img src='img/down.svg'><span>Download</span></th>
					<th><img src='img/visualizer.svg'><span>Visualizer</span></th>
					<th><img src='img/remote.svg'><span>Remote Player</span></th>
				</tr>
				<tr>
					<td><button id='btnDownloadTrack' class='btnPadding' onclick='download(obj("mainPlayer").src + "&download=true");' title='Download the current track'>Current Track</button></td>
					<td>
						<select id="visualizerSwitcher" class='right btnMarginLeft' onchange="setVisualizer(this.value);">
							<?php
							$files = scandir('js/visualizers/');
							foreach($files as $file) {
								if (substr($file,0,1) == ".") continue;
								echo "<option>" . $file . "</option>";
							}
							?>
						</select>
					</td>
					<td>
						<select id='remotePlay' onchange='setRemotePlayer(this); sendAllRemotePlayerParameters(); fadeAudioOut("mainPlayer", false);'>
						<option value='-1'>Local only</option>
						<?php
						require_once('database.php');
						$sql = "SELECT * FROM remote r ";
						$statement = $mysqli->prepare($sql);
						$statement->execute();
						$result = $statement->get_result();
						if($result->num_rows == 0) echo "<option disabled>No remote players</option>";
						else while ($row = $result->fetch_object()) echo "<option value='" . $row->id . "'>" . $row->title . "</option>\n";
						?>
						</select>
					</td>
				</tr>
			</table>
			<h2>Current playlist</h2>
			<ul id="playlist" class="list">
				<?php if($currentplaylist != "") { ?>
					<?php echo $currentplaylist; ?>
				<?php } else { ?>
					Current playlist is empty. Please select a track in the library to play.
				<?php } ?>
			</ul>
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
							<?php if($currentcover!="") echo "<img src='$currentcover'>"; ?>
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

		<div id="controls" class="albumtooltip">
			<button id="btnPrev" class="roundButton roundButtonSmall" onClick="nextTrack(false);" onfocus="controlButton_onFocus();" onblur="controlButton_lostFocus();">prev</button>
			<button id="btnPlayPause" class="roundButton roundButtonBig" onClick="togglePlayPause();" onfocus="controlButton_onFocus();" onblur="controlButton_lostFocus();">&nbsp;</button>
			<button id="btnNext" class="roundButton roundButtonSmall" onClick="nextTrack(true);" onfocus="controlButton_onFocus();" onblur="controlButton_lostFocus();">next</button>
			<?php if($currentAlbum!="") { ?>
			<span id="titleDetails" class="tooltipcontent" style="<?php if(!$detailsbox) echo 'display:none'; ?>">
				<div id="albumimgDetails"><?php if($currentcover!="") echo "<img src='$currentcover'>"; ?></div>
				<div class="textcontainer">
					<!--
					<div id="titleTitleDetails" class="title"><?php echo $currentTitle; ?></div>
					<div id="titleArtistDetails" class="artist"><?php echo $currentArtist; ?></div>
					-->
					<div id="titleAlbumDetails" class="album"><?php echo $currentAlbum; ?></div>
				</div>
			</span>
			<?php } ?>
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
		<?php if(!(getOS1() == "iPhone" && getBrowser1() == "Safari")) { ?>
		<div class="volumetooltip">
			<button id="btnVolume" class="roundButton roundButtonSmall" title="Show or hide volume slider">&nbsp;</button>
			<div class="tooltipcontent">
				<input type="range" id="volumeBar" min="0" max="1" step="0.01" value="<?php echo isset($_SESSION['volume']) ? $_SESSION['volume'] : "0.8"; ?>" onchange="setVolume();"></input>
			</div>
		</div>
		<?php } ?>
		<button id="btnFullscreen" class="roundButton roundButtonSmall" onclick="toggleFullscreen();" title="Toggle fullscreen mode">&nbsp;</button>
		<button id="btnCurrentPlaylist" class="roundButton roundButtonSmall" onclick="toggleCurrentPlaylist();" title="Show current playlist">&nbsp;</button>
		<button id="btnMenu" class="roundButton roundButtonSmall" onclick="toggleMenu('<?php echo $libparam_scrollto; ?>');" title="Show Library">&nbsp;</button>
	</div>

	<div id="notification">
	</div>

	<script>initPlaylist(<?php echo ($currenttrack_number-1); ?>);setVolume();</script>

	<?php if($currentURL == "") echo "<script>toggleMenu('');</script>"; ?>

</body>
</html>
