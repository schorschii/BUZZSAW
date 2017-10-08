<?php

require_once('session.php');
require_once('global.php');


if (isset($_GET['title'])) {
	require_once('database.php');
	$sql = "SELECT tr.cover AS 'cover' "
	     . "FROM track tr "
	     . "WHERE tr.id = ?";
	$statement = $mysqli->prepare($sql);
	$track_id = $_GET['title'];
	$statement->bind_param('i', $track_id);
	$statement->execute();
	$result = $statement->get_result();

	$cover = "";
	while($row = $result->fetch_object()) {
		$cover = $row->cover;
		break;
	}

	if ($cover != "") {
		echo "<img src=\"";
		echo $cover;
		echo "\">";
	} else
		echo "<!-- no cover -->";
}

?>
