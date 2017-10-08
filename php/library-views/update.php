<?php

if(!isset($view)) die("Access this page using library.php with the view parameter!");
require_once('global.php');

?>

<?php if(isUpdateAvail()) { ?>
	<li class='option tracklisting'>
		<div class='about'>
			<p>
				<img id='aboutLogo' src='img/buzzsaw_icon_b.svg'>
				<b>A new BUZZSAW version is available.</b>
				<br>Please update soon to the new version.</a>
				<!--<br><i>New Version: <?php echo $NVERSION; ?></i>
				<br><i>Current Version: <?php echo $CVERSION; ?></i>-->
			</p>
		</div>
	</li>

	<li class='option tracklisting'>
		<a href='https://github.com/schorschii/buzzsaw' target='_blank'><img src='img/down.svg'>Download the update package on GitHub</a>
	</li>
<?php } else { ?>
	<li class='option tracklisting'>
		<div class='about'>
			<p>
				<b>Your BUZZSAW installation is up to date.</b>
			</p>
		</div>
	</li>
<?php } ?>
