<?php
	$sql = "SELECT tr.id as 'id', tr.track_number as 'track_number', tr.title as 'title', al.title as 'album_title', ar.title as 'artist_title' FROM track tr "
	     . "INNER JOIN album al ON tr.album_id = al.id "
	     . "INNER JOIN artist ar ON tr.artist_id = ar.id "
	     . "ORDER BY tr.inserted DESC LIMIT 500";
	$result = $mysqli->query($sql);
	while($row = $result->fetch_object()) {
		$linkaction = "href='player.php?currentplaylist=recentlyadded&track=" . $row->id . "'";
		if (isset($_GET['addtoplaylist']))
			$linkaction = "onclick='ajaxRequest(\"notification\",\"playlistedit.php?title=" . $row->id . "&addtoplaylist=" . $_GET['addtoplaylist'] . "\"); clearNotification();'";

		$track_number = "";
		if (isset($_GET['album']))
			$track_number = "<span class='track_number'>" . $row->track_number . "</span> ";

		$shorttitle = shortText($row->title);

		$title = $row->title . "\n" . $row->album_title . "\n" . $row->artist_title;

		echo "<li><a $linkaction title='$title'>" . $track_number . $shorttitle . "</a></li>";
	}
?>
