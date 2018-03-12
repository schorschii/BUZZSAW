<?php

if(!isset($view)) die("Access this page using library.php with the view parameter!");

?>

<?php

		$artistfilter = "";
		if (isset($_GET['artist'])) {
			$artistfilter = "WHERE artist_id = " . $mysqli->real_escape_string($_GET['artist']);

			// create artist header before album listing
			$sql = "SELECT * FROM artist WHERE id = " . $mysqli->real_escape_string($_GET['artist']) . " LIMIT 1";
			$result = $mysqli->query($sql);
			$currentartist = ""; $currentartistid = -1;
			while($row = $result->fetch_object()) {
				$currentartist = $row->title;
				$currentartistid = $row->id;
			}

			echo "<li class='tracklisting'>";
			echo "<a href='#' title=\"back to artists\" onclick=\"ajaxRequest('content','library.php?view=artist$addtoplaylistparameter','$currentartistid');\">";
			echo "<span class='track_number albumdescription'>&lt;</span>";
			echo "<span class='albumdescription'><span class='track_number albumdescription'>$currentartist</span></span>";
			echo "</a>";
			echo "</li>";

			// all tracks-link for this artist
			echo "<li>";
			echo "<a href='#' onclick='ajaxRequest(\"content\",\"library.php?view=track&artist=" . $currentartistid. "$addtoplaylistparameter\");'>";
			echo "<img class='cover_libraray' src='img/track.svg'>";
			echo "All tracks from this artist";
			echo "</a>";
			echo "</li>";
		}
		$sql = "SELECT al.id as 'id', al.title as 'title', ar.title as 'artist_title' "
		     . "FROM album al "
		     . "INNER JOIN artist ar ON ar.id = al.artist_id "
		     . "$artistfilter ORDER BY title ASC";
		$result = $mysqli->query($sql);
		while($row = $result->fetch_object()) {
			// get cover from first track of this album
			$cover = ""; $cover_class = "cover_libraray";
			$sql = "SELECT * FROM track WHERE album_id = " . $row->id . " LIMIT 1;";
			$result2 = $mysqli->query($sql);
			while($row2 = $result2->fetch_object()) {
				$cover = $row2->cover;
			}
			if ($cover == "") { $cover = "img/album.svg"; $cover_class = "cover_libraray unknown"; }

			$title = $row->title . "\n" . $row->artist_title;

			echo "<li>";
			echo "<a href='#' onclick='ajaxRequest(\"content\",\"library.php?view=track&album=" . $row->id . "$addtoplaylistparameter\");' title='".htmlspecialchars($title,ENT_QUOTES)."'>";
			echo "<img class='$cover_class' src='$cover'>";
			echo shortText($row->title);
			echo "</a>";
			echo "</li>";
		}

?>
