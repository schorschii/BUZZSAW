<?php

if(!isset($view)) die("Access this page using library.php with the view parameter!");

?>

<?php

		$currentplaylist = "album";
		$class = "";
		$showshorttitle = true;
		$sql = "SELECT genre FROM track GROUP BY genre ORDER BY genre";
		$result = $mysqli->query($sql);
		while($row = $result->fetch_object()) {
			if($row->genre === null || $row->genre === "")
				continue;

			if ($showshorttitle)
				$shorttitle = shortText($row->genre);
			else
				$shorttitle = $row->genre;

			$title = $row->genre;

			echo "<li class='$class'><a href='#' onclick='ajaxRequest(\"content\",\"library.php?view=track&genre=" . urlencode($row->genre) . "\");' title='".htmlspecialchars($title,ENT_QUOTES)."'>" . $shorttitle . "</a></li>";
		}

?>
