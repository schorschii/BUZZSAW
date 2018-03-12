<?php

if(!isset($view)) die("Access this page using library.php with the view parameter!");

?>

<?php

		$sql = "SELECT ar.id AS 'id', ar.title AS 'title', "
		     . "(SELECT cover FROM track WHERE track.artist_id=ar.id AND track.cover IS NOT NULL LIMIT 1) AS 'cover', "
		     . "(SELECT COUNT(id) FROM album WHERE album.artist_id=ar.id) AS 'album_count', "
		     . "(SELECT id FROM album WHERE album.artist_id=ar.id LIMIT 1) AS 'first_album_id' "
		     . "FROM artist ar "
		     . "ORDER BY title ASC";
		$result = $mysqli->query($sql);
		while($row = $result->fetch_object()) {
			// get a cover from first track of this album
			$cover = $row->cover;
			$cover_class = "artistimg_libraray";
			if ($cover == "") {
				$cover = "img/artist.svg";
				$cover_class = "artistimg_libraray unknown";
			}

			$linkaction = "onclick='ajaxRequest(\"content\",\"library.php?view=album&artist=" . $row->id . "$addtoplaylistparameter\");'";

			if($row->album_count == 1) {
				$linkaction = "onclick='ajaxRequest(\"content\",\"library.php?view=track&album=" . $row->first_album_id . "$addtoplaylistparameter\");'";
			}

			echo "<li>";
			echo "<a href='#' id='element_" . $row->id . "' $linkaction title='" . htmlspecialchars($row->title,ENT_QUOTES) . "'>";
			echo "<img class='$cover_class' src='" . $cover . "'>";
			echo shortText($row->title);
			echo "</a>";
			echo "</li>";
		}

?>
