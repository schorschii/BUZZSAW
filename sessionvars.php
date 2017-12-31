<?php
session_start();

if (isset($_SESSION['username'])) {
	if(isset($_GET['set']) && isset($_GET['value'])) {
		switch($_GET['set']) {
			case "volume":
				$_SESSION['volume'] = $_GET['value'];
				exit;
		}
	}

	if(isset($_GET['get']) && isset($_GET['value'])) {
		switch($_GET['get']) {
			case "volume":
				return $_SESSION['volume'];
				exit;
		}
	}
}
?>
