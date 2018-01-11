<?php
	require_once('session.php');
	require_once('global.php');
?>

<?php
	$view = "artist";
	if (isset($_GET['view']))
		$view = $_GET['view'];

	$scrollto_active = false;
	if (isset($_GET['scrollto_active']) && $_GET['scrollto_active'] != "")
		$scrollto_active = true;

	$addtoplaylistparameter = "";
	if (isset($_GET['addtoplaylist']))
		$addtoplaylistparameter = "&addtoplaylist=" . $_GET['addtoplaylist'];
?>

<div id="containerContainer">
	<table id="libraryViewModeBar" cellspacing="0">
		<tr>
			<td>
				<a href='#' onclick="ajaxRequest('content','library.php?view=artist<?php echo $addtoplaylistparameter; ?>');" class="<?php $imgadd=""; if ($view =='artist') { echo 'active'; $imgadd="_b"; } ?>">
					<img src="img/artist<?php echo $imgadd; ?>.svg" height="30"><br>Artists
				</a>
			</td>
			<td>
				<a href='#' onclick="ajaxRequest('content','library.php?view=album<?php echo $addtoplaylistparameter; ?>');" class="<?php $imgadd=""; if ($view =='album') { echo 'active'; $imgadd="_b"; } ?>">
					<img src="img/album<?php echo $imgadd; ?>.svg" height="30"><br>Albums
				</a>
			</td>
			<td>
				<a href='#' onclick="ajaxRequest('content','library.php?view=track<?php echo $addtoplaylistparameter; ?>');" class="<?php $imgadd=""; if ($view =='track') { echo 'active'; $imgadd="_b"; } ?>">
					<img src="img/track<?php echo $imgadd; ?>.svg" height="30"><br>Tracks
				</a>
			</td>
			<td>
				<a href='#' onclick="ajaxRequest('content','library.php?view=playlist');" class="<?php $imgadd=""; if ($view =='playlist') { echo 'active'; $imgadd="_b"; } ?>">
					<img src="img/currentplaylist<?php echo $imgadd; ?>.svg" height="30"><br>Playlists
				</a>
			</td>
			<td>
				<a href='#' onclick="ajaxRequest('content','library.php?view=files');" class="<?php $imgadd=""; if ($view =='files') { echo 'active'; $imgadd="_b"; } ?>">
					<img src="img/dir<?php echo $imgadd; ?>.svg" height="30"><br>Files
				</a>
			</td>
			<td>
				<a href='#' onclick="ajaxRequest('content','library.php?view=recentlyadded');" class="<?php $imgadd=""; if ($view =='recentlyadded') { echo 'active'; $imgadd="_b"; } ?>">
					<img src="img/recent<?php echo $imgadd; ?>.svg" height="30"><br>Recent
				</a>
			</td>
			<td>
				<a href='#' onclick="ajaxRequest('content','library.php?view=options');" class="<?php $imgadd=""; if ($view =='options') { echo 'active'; $imgadd="_b"; } ?>">
					<img src="img/menu<?php echo $imgadd; ?>.svg" height="30"><br>Options
				</a>
			</td>
			<?php if(isUpdateAvail()) { ?>
				<td>
					<a href='#' onclick="ajaxRequest('content','library.php?view=update');" class="<?php $imgadd=""; if ($view =='update') { echo 'active'; $imgadd="_b"; } ?>">
						<img src="img/update<?php echo $imgadd; ?>.svg" height="30"><br>Update
					</a>
				</td>
			<?php } ?>
		</tr>
	</table>

	<div id="searchContainer" class="inputwithimg">
		<img src="img/search.svg">
		<input type="text" id="search" onkeyup="searchList('search', 'mainlist');" placeholder="" <?php if(!$scrollto_active) echo "autofocus='true'"; ?> title="search current library view" onfocus="controlButton_onFocus();" onblur="controlButton_lostFocus();">
	</div>
	<ul id="mainlist" class="list">
	<?php
	require_once('database.php');

	// add track to playlist if parameter is given
	if (isset($_GET['addtoplaylist'])) {
		$sql = "SELECT title FROM playlist WHERE id = ?;";
		$statement = $mysqli->prepare($sql);
		if (!$statement)
			die("<b>PREPARE FAILED:</b>&nbsp;$file<br>$sql<br>");
		if (!$statement->bind_param('i', $_GET['addtoplaylist']))
			die("<b>BIND FAILED:</b>&nbsp;$file<br>$sql<br>");
		if (!$statement->execute())
			die("<b>EXEC FAILED:</b>&nbsp;$file<br>$sql<br>");
		$statement->bind_result($title);
		while ($statement->fetch()) {
			echo "<div class='playlistOptions'>";
			echo "<div class='infobox'>";
			echo "Add tracks to playlist <span class='playlist_title'>$title</span>";
			echo "<a href='#' onclick='ajaxRequest(\"content\",\"library.php?view=artist\");' class='playlist_abort'>&#x2716;&nbsp;Abort</a>";
			echo "</div>";
			echo "</div><br>";
			break;
		}
		$statement->close();
	}


	// open library view
	if(file_exists("php/library-views/$view.php")) {
		require("php/library-views/$view.php");
	} elseif ($view == "license") {
		echo "<div id='license'>";
		echo file_get_contents("LICENSE.txt");
		echo "</div>";
	} else {
		echo "invalid library call :-(";
	}

	?>
	</ul>
</div>
