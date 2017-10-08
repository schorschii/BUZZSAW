<?php

if(!isset($view)) die("Access this page using library.php with the view parameter!");

?>

<h1>Upload to the music server</h1>
<h2>Upload file(s)</h2>
<form method="POST" enctype="multipart/form-data" action="upload.php">
	<input name="uploads[]" type="file" multiple="multiple" />
	<input type="submit" value="Upload" />
</form>
<h2>Upload folder</h2>
<form method="POST" enctype="multipart/form-data" action="upload.php">
	<input type="file" name="directory[]" webkitdirectory directory multiple />
	<input type="submit" value="Upload" />
</form>
