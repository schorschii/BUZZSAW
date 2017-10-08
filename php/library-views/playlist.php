<?php

if(!isset($view)) die("Access this page using library.php with the view parameter!");

?>

<?php

		echo "<div class='playlistOptions'>";
		echo "<button id='btnPlaylistNew' class='roundButton roundButtonSmall btnAdd btnPadding' onclick='createPlaylist();' title='Create a new playlist'>Create new playlist</button>";
		echo "</div><br>";

		if (isset($_GET['new_playlist_name'])) {
			$sql = "INSERT INTO playlist (title, type) VALUES ('" . $mysqli->real_escape_string($_GET['new_playlist_name']) . "', 0);";
			if (!$mysqli->multi_query($sql))
				die("<b>ERROR CREATING PLAYLIST:</b><br>" . $mysqli->error . "<br>");
		} elseif (isset($_GET['remove_playlist'])) {
			$sql = "DELETE FROM playlist WHERE id='" . $mysqli->real_escape_string($_GET['remove_playlist']) . "';";
			if (!$mysqli->multi_query($sql))
				die("<b>ERROR REMOVING PLAYLIST:</b><br>" . $mysqli->error . "<br>");
		}

		$sql = "SELECT * FROM playlist";
		$result = $mysqli->query($sql);
		$counter = 0;
		while($row = $result->fetch_object()) {
			echo "<li>";
			echo "<a href='#' onclick='ajaxRequest(\"content\",\"library.php?view=playlist_content&playlist=" . $row->id . "\");'>";
			echo "<img class='cover_libraray unknown' src='img/currentplaylist.svg'>";
			echo $row->title;
			echo "</a>";
			echo "</li>";
			$counter ++;
		}
		if ($counter == 0) echo "<li>You don't have any playlists.</li>";

?>
