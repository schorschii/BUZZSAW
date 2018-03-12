<?php

if(!isset($view)) die("Access this page using library.php with the view parameter!");

?>

<?php

		$currentplaylist = "album";
		$class = "";
		$showshorttitle = true;
		$albumfilter = "ORDER BY title ASC";
		if (isset($_GET['genre'])) {
			$albumfilter = "WHERE tr.genre = '" . $mysqli->real_escape_string($_GET['genre']) . "' ORDER BY title ASC";
		}
		if (isset($_GET['album'])) {
			$class = "tracklisting";
			$showshorttitle = false;
			$albumfilter = "WHERE album_id = " . $mysqli->real_escape_string($_GET['album']) . " ORDER BY track_number, title ASC";

			// create album header before track listing
			$sql = "SELECT * FROM album WHERE id = " . $mysqli->real_escape_string($_GET['album']);
			$result = $mysqli->query($sql);
			$currentalbum = ""; $currentartistid = "";
			while($row = $result->fetch_object()) {
				$currentalbum = $row->title;
				$currentartistid = $row->artist_id;
			}
			$sql = "SELECT * FROM track WHERE album_id = " . $mysqli->real_escape_string($_GET['album']) . " LIMIT 1";
			$result = $mysqli->query($sql);
			$currentcover = "img/album.svg"; $currentcoverclass = "cover_big unknown";
			while($row = $result->fetch_object()) {
				if ($row->cover != "") {
					$currentcover = $row->cover;
					$currentcoverclass = "cover_big";
				}
			}
			$sql = "SELECT * FROM artist WHERE id = " . $currentartistid . " LIMIT 1";
			$result = $mysqli->query($sql);
			$currentartist = "";
			while($row = $result->fetch_object()) {
				$currentartist = $row->title;
			}

			echo "<li class='$class'>";
			echo "<a href='#' title=\"back to artist's albums\" onclick=\"ajaxRequest('content','library.php?view=album&artist=$currentartistid".$addtoplaylistparameter."');\">";
			echo "<span class='track_number albumdescription'>&lt;</span>";
			echo "<img class='$currentcoverclass' src='$currentcover'>";
			echo "<span class='albumdescription'><span class='track_number albumdescription'>$currentartist</span><br>$currentalbum</span>";
			echo "</a>";
			echo "</li>";
		}
		if (isset($_GET['artist'])) {
			$albumfilter = "WHERE ar.id = " . $mysqli->real_escape_string($_GET['artist']) . " ORDER BY album_id, track_number, title ASC";
			$currentplaylist = "artist";

			$currentartistid = $_GET['artist'];
			$sql = "SELECT * FROM artist WHERE id = " . $currentartistid . " LIMIT 1";
			$result = $mysqli->query($sql);
			$currentartist = "";
			while($row = $result->fetch_object()) {
				$currentartist = $row->title;
			}

			echo "<li class='tracklisting'>";
			echo "<a href='#' title=\"back to artist's albums\" onclick=\"ajaxRequest('content','library.php?view=album&artist=$currentartistid".$addtoplaylistparameter."');\">";
			echo "<span class='track_number albumdescription'>&lt;</span>";
			echo "<span class='albumdescription'><span class='track_number albumdescription'>All tracks from $currentartist</span></span>";
			echo "</a>";
			echo "</li>";
		}
		$sql = "SELECT tr.id as 'id', tr.track_number as 'track_number', tr.title as 'title', al.title as 'album_title', ar.title as 'artist_title' "
		     . "FROM track tr "
		     . "INNER JOIN album al ON tr.album_id = al.id "
		     . "INNER JOIN artist ar ON tr.artist_id = ar.id "
		     . "$albumfilter ";
		$result = $mysqli->query($sql);
		while($row = $result->fetch_object()) {
			$linkaction = "href='player.php?currentplaylist=$currentplaylist&track=" . $row->id . "'";
			if (isset($_GET['addtoplaylist']))
				$linkaction = "onclick='ajaxRequest(\"notification\",\"playlistedit.php?title=" . $row->id . "&addtoplaylist=" . $_GET['addtoplaylist'] . "\"); clearNotification();'";

			$track_number = "";
			if (isset($_GET['album']))
				$track_number = "<span class='track_number'>" . $row->track_number . "</span> ";

			if ($showshorttitle)
				$shorttitle = shortText($row->title);
			else
				$shorttitle = $row->title;

			$title = $row->title . "\n" . $row->album_title . "\n" . $row->artist_title;

			echo "<li class='$class'><a $linkaction oncontextmenu='return openContextMenu(".$row->id.")' title='".htmlspecialchars($title,ENT_QUOTES)."'>" . $track_number . $shorttitle . "</a></li>";
		}

?>
