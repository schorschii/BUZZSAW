<?php

if(!isset($view)) die("Access this page using library.php with the view parameter!");

?>

<?php

	require_once('global.php');
	$searchpath = MEDIAROOT;
	if(isset($_GET['searchpath']) && is_sub_dir($_GET['searchpath'], $searchpath)) {
		$searchpath = $_GET['searchpath'];
	}
		

		foreach(scandir($searchpath) as $file) {
			if($file === ".") continue;
			if($file === ".." && realpath($searchpath) === realpath(MEDIAROOT)) continue;

			$linkaction = "";
			$imgsrc = "";
			$shorttitle = shortText($file);
			if(is_dir("$searchpath/$file")) {
				$linkaction = "href='#' onclick='ajaxRequest(\"content\",\"library.php?view=files&searchpath=" . "$searchpath/$file" . "\");'";
				$imgsrc = "img/dir.svg";
				echoEntry($linkaction, $imgsrc, $shorttitle, $file);
			}
			elseif(isAudioFile("$searchpath/$file")) {
				$linkaction = "href='player.php?currentplaylist=dir&track=" . "$searchpath/$file" . "'";
				$imgsrc = "img/track.svg";
				echoEntry($linkaction, $imgsrc, $shorttitle, $file);
			}
		}

	function echoEntry($linkaction, $imgsrc, $shorttitle, $file) {
		echo "<li>";
		echo "<a $linkaction title='" . $file . "'>";
		echo "<img class='artistimg_libraray unknown' src='$imgsrc'>";
		echo $shorttitle;
		echo "</a>";
		echo "</li>";
	}

?>
