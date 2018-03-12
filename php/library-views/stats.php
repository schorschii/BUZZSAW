<?php

if(!isset($view)) die("Access this page using library.php with the view parameter!");
require_once('global.php');

?>

<?php
$tracks = 0;
$tracks_cover = 0;
$tracks_playtime = 0;
$tracks_length = 0;
$albums = 0;
$artists = 0;
$playlists = 0;
$sql = "SELECT 'tracks' AS 'key', COUNT(*) AS 'value' FROM track "
     . "UNION SELECT 'tracks_cover' AS 'key', COUNT(cover) as 'value' FROM track "
     . "UNION SELECT 'tracks_playtime' AS 'key', SUM(duration) as 'value' FROM track "
     . "UNION SELECT 'tracks_length' AS 'key', SUM(length) as 'value' FROM track "
     . "UNION SELECT 'albums' AS 'key', COUNT(*) as 'value' FROM album "
     . "UNION SELECT 'artists' AS 'key', COUNT(*) as 'value' FROM artist "
     . "UNION SELECT 'playlists' AS 'key', COUNT(*) as 'value' FROM playlist";
	$result = $mysqli->query($sql);
	while($row = $result->fetch_object()) {
		if($row->key === "tracks")
			$tracks = $row->value;
		if($row->key === "tracks_cover")
			$tracks_cover = $row->value;
		if($row->key === "tracks_playtime")
			$tracks_playtime = $row->value;
		if($row->key === "tracks_length")
			$tracks_length = $row->value;
		if($row->key === "albums")
			$albums = $row->value;
		if($row->key === "artists")
			$artists = $row->value;
		if($row->key === "playlists")
			$playlists = $row->value;
	}

function formatBytesDecimal($bytes, $precision = 2) {
	$units = array('B', 'KB', 'MB', 'GB', 'TB'); 
	$bytes = max($bytes, 0); 
	$pow = floor(($bytes ? log($bytes) : 0) / log(1000)); 
	$pow = min($pow, count($units) - 1); 

	$bytes /= pow(1000, $pow);
	// $bytes /= (1 << (10 * $pow)); 

	return round($bytes, $precision) . ' ' . $units[$pow]; 
}
function formatBytesBinary($bytes, $precision = 2) {
	$units = array('B', 'KiB', 'MiB', 'GiB', 'TiB'); 
	$bytes = max($bytes, 0); 
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
	$pow = min($pow, count($units) - 1); 

	$bytes /= pow(1024, $pow);
	// $bytes /= (1 << (10 * $pow)); 

	return round($bytes, $precision) . ' ' . $units[$pow]; 
}
function formatSeconds($seconds) {
	$days        = $seconds / (60*60*24);
	$days_mod    = $seconds % (60*60*24);

	$hours       = $days_mod / (60*60);
	$hours_mod   = $days_mod % (60*60);

	$minutes     = $hours_mod / (60);
	$minutes_mod = $hours_mod % (60);

	$string = floor($days) ." days, "
	        . floor($hours)." hours, "
	        . floor($minutes)." minutes, "
	        . floor($minutes_mod). " seconds";
	return $string;
}
?>

<table class="statistics">
	<tr>
		<th>Library Size:</th>
		<td>
			<?php echo formatBytesBinary($tracks_length) . ' / ' . formatBytesDecimal($tracks_length); ?>
			<br>
			<?php echo '~ '.number_format(ceil($tracks_length/1440000),0,',','.') . ' floppy disks (3,5")'; ?>
			<br>
			<?php echo '('.number_format($tracks_length,0,',','.').' bytes total)'; ?>
		</td>
	</tr>
	<tr>
		<th>Overall Playtime:</th>
		<td>
			<?php echo formatSeconds($tracks_playtime); ?>
			<br>
			<?php echo '~ '.number_format(ceil($tracks_playtime/60/120),0,',','.').' compact cassettes (C120)'; ?>
			<br>
			<?php echo '('.number_format($tracks_playtime,0,',','.').' seconds total)'; ?>
		</td>
	</tr>
	<tr>
		<th>Tracks:</th>
		<td><?php echo $tracks; ?></td>
	</tr>
	<tr>
		<th>&#8627; with cover art:</th>
		<td><?php echo $tracks_cover; ?></td>
	</tr>
	<tr>
		<th>Albums:</th>
		<td><?php echo $albums; ?></td>
	</tr>
	<tr>
		<th>Artists:</th>
		<td><?php echo $artists; ?></td>
	</tr>
	<tr>
		<th>Playlists:</th>
		<td><?php echo $playlists; ?></td>
	</tr>
</table>
