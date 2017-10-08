<?php
	require_once('session.php');

	switch ($_SESSION['logintype']) {
		case 1:
			header('Location: player.php');
			break;
		case 2:
			header('Location: remoteplayer.php');
			break;
		case 3:
			header('Location: vote.php');
			break;
	}
?>
