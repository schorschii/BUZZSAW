<?php

if(!isset($view)) die("Access this page using library.php with the view parameter!");

?>

<?php

		// remove track from playlist if requested
		if (isset($_GET['remove_playlist_track'])) {
			$sql = "DELETE FROM playlist_track "
			     . "WHERE id = " . $mysqli->real_escape_string($_GET['remove_playlist_track']) . ";";
			$result = $mysqli->query($sql);
			if(!$result) echo "ERROR DELETING TRACK";
		}
		// move track up if requested
		if (isset($_GET['moveup_playlist_track'])) {
			$sql = "CALL MoveTrackInPlaylist(" . $mysqli->real_escape_string($_GET['moveup_playlist_track']) . ",-1);";
			$result = $mysqli->query($sql);
			if(!$result) echo "ERROR MOVING TRACK";
		}
		// move track down if requested
		if (isset($_GET['movedown_playlist_track'])) {
			$sql = "CALL MoveTrackInPlaylist(" . $mysqli->real_escape_string($_GET['movedown_playlist_track']) . ",1);";
			$result = $mysqli->query($sql);
			if(!$result) echo "ERROR MOVING TRACK";
		}

		$playlistname = "";
		$sql = "SELECT p.title AS 'title' FROM playlist p "
		     . "WHERE id = " . $mysqli->real_escape_string($_GET['playlist']) . " "
		     . "LIMIT 1;";
		$result = $mysqli->query($sql);
		$counter = 0;
		while($row = $result->fetch_object()) {
			$playlistname = $row->title;
			break;
		}

		echo "<div class='playlistOptions'>";
		echo "<img class='cover_libraray unknown' src='img/currentplaylist.svg'><span class='playlist_title'>$playlistname</span>";
		echo "<button id='btnRemovePlaylist' class='roundButton roundButtonSmall btnTrash right btnMarginLeft' onclick='removePlaylist(" . $_GET['playlist'] . ");' title='Remove this playlist'></button>";
		echo "<button id='btnMoveTrackDown' class='roundButton roundButtonSmall btnMoveDown right btnMarginLeft' onclick='ajaxRequest(\"content\",\"library.php?view=playlist_content&playlist=" . $mysqli->real_escape_string($_GET['playlist']) . "&action=movedown\");' title='Move track(s) down in playlist'></button>";
		echo "<button id='btnMoveTrackUp' class='roundButton roundButtonSmall btnMoveUp right btnMarginLeft' onclick='ajaxRequest(\"content\",\"library.php?view=playlist_content&playlist=" . $mysqli->real_escape_string($_GET['playlist']) . "&action=moveup\");' title='Move track(s) up in playlist'></button>";
		echo "<button id='btnRemovePlaylistTrack' class='roundButton roundButtonSmall btnRemove right btnMarginLeft' onclick='ajaxRequest(\"content\",\"library.php?view=playlist_content&playlist=" . $mysqli->real_escape_string($_GET['playlist']) . "&action=remove\");' title='Remove track(s) from playlist'></button>";
		echo "<button id='btnAddTrack' class='roundButton roundButtonSmall btnAdd right btnMarginLeft' onclick='ajaxRequest(\"content\",\"library.php?view=artist&addtoplaylist=" . $mysqli->real_escape_string($_GET['playlist']) . "\");' title='Add track(s) to playlist'></button>";
		echo "</div><br>";
		$action = "";
		if(isset($_GET['action'])) {
			if($_GET['action'] == "remove") {
				echo "<div class='infobox error inlistinfo'>Please click on a track to remove it from this playlist.</div>";
				$action = "remove";
			} elseif($_GET['action'] == "moveup") {
				echo "<div class='infobox inlistinfo'>Please click on a track to move it up.</div>";
				$action = "moveup";
			} elseif($_GET['action'] == "movedown") {
				echo "<div class='infobox inlistinfo'>Please click on a track to move it down.</div>";
				$action = "movedown";
			}
		}

		$sql = "SELECT @curRow := @curRow + 1 AS 'rank', "
		     . "pt.sequence AS 'sequence', "
		     . "pt.track_id AS 'track_id', "
		     . "pt.id AS 'id', "
		     . "t.title AS 'title' "
		     . "FROM playlist_track pt "
		     . "INNER JOIN track t ON pt.track_id = t.id "
		     . "JOIN  (SELECT @curRow := 0) r "
		     . "WHERE pt.playlist_id = " . $mysqli->real_escape_string($_GET['playlist']) . " "
		     . "ORDER BY pt.sequence;";
		$result = $mysqli->query($sql);
		$counter = 0;
		while($row = $result->fetch_object()) {
			$linkaction = "href='player.php?currentplaylist=playlist&playlist=" . $mysqli->real_escape_string($_GET['playlist']) . "&track=" . $row->track_id . "'";
			if($action === "remove")
				$linkaction = "href='#' onclick='removeTrackFromPlaylist(\"" . $mysqli->real_escape_string($_GET['playlist']) . "\",\"" . $row->id . "\");'";
			elseif($action === "moveup")
				$linkaction = "href='#' onclick='moveTrackUpPlaylist(\"" . $mysqli->real_escape_string($_GET['playlist']) . "\",\"" . $row->id . "\");'";
			elseif($action === "movedown")
				$linkaction = "href='#' onclick='moveTrackDownPlaylist(\"" . $mysqli->real_escape_string($_GET['playlist']) . "\",\"" . $row->id . "\");'";

			echo "<li>";
			echo "<a class='first' $linkaction>";
			echo "<span class='track_number'>" . $row->rank . "</span> ";
			echo $row->title;
			echo "</a>";
			echo "</li>";
			$counter ++;
		}
		if ($counter == 0) echo "<li>This playlist contains no tracks.</li>";

?>
