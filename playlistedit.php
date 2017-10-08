<?php

require_once('session.php');


require_once('database.php');

if(isset($_GET['addtoplaylist']) && isset($_GET['title'])) {
	echo "<div class='infobox ok'>";
	$new_sequence = 0;

	// step 1: get last sequence number
	$sql = "SELECT sequence FROM playlist_track WHERE playlist_id = ? ORDER BY sequence DESC LIMIT 1;";
	$statement = $mysqli->prepare($sql);
	if ($statement) {
		if ($statement->bind_param('i', $_GET['addtoplaylist'])) {
			if ($statement->execute()) {
				$result = $statement->get_result();
				while($row = $result->fetch_object()) {
					$new_sequence = $row->sequence+1;
					break;
				}
			} else {
				echo "<b>EXEC FAILED:</b>&nbsp;".$statement->error."<br>$sql<br>";
			}
		} else {
			echo "<b>BIND FAILED:</b>&nbsp;".$statement->error."<br>$sql<br>";
		}
	} else {
		echo "<b>PREPARE FAILED:</b>&nbsp;<br>$sql<br>";
	}

	// step 2: insert track into playlist
	$sql = "INSERT INTO playlist_track(playlist_id, track_id, sequence) VALUES (?, ?, ?);";
	$statement = $mysqli->prepare($sql);
	if ($statement) {
		if ($statement->bind_param('iii', $_GET['addtoplaylist'], $_GET['title'], $new_sequence)) {
			if ($statement->execute()) {
				echo "Title was added to playlist.";
			} else {
				echo "<b>EXEC FAILED:</b>&nbsp;".$statement->error."<br>$sql<br>";
			}
		} else {
			echo "<b>BIND FAILED:</b>&nbsp;".$statement->error."<br>$sql<br>";
		}
	} else {
		echo "<b>PREPARE FAILED:</b>&nbsp;<br>$sql<br>";
	}

	echo "</div>";
} else {
	echo "<div class='infobox error'>";
	echo "Action could not be executed - missing parameter";
	echo "</div>";
}

?>
