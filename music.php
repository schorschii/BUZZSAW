<?php

require_once('global.php');
set_time_limit ( 10 );


/* Source https://gist.github.com/kosinix/4cf0d432638817888149 */
class ResumeDownload {
	private $file;
	private $name;
	private $boundary;
	private $content_type;
	private $delay = 0;
	private $size = 0;
	function __construct($file, $content_type = "application/octet-stream", $delay = 0) {
	    if (! is_file($file)) {
	        header("HTTP/1.1 400 Invalid Request");
	        die("<h3>File Not Found</h3>");
	    }
	    $this->size = filesize($file);
	    $this->file = fopen($file, "r");
	    $this->boundary = md5($file);
	    $this->delay = $delay;
	    $this->name = basename($file);
	    $this->content_type = $content_type;
	}
	public function process() {
	    $ranges = NULL;
	    $t = 0;
	    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_SERVER['HTTP_RANGE']) && $range = stristr(trim($_SERVER['HTTP_RANGE']), 'bytes=')) {
	        $range = substr($range, 6);
	        $ranges = explode(',', $range);
	        $t = count($ranges);
	    }
	    header("Accept-Ranges: bytes");
	    header("Content-Type: " . $this->content_type);
	    header("Content-Transfer-Encoding: binary");
	    header(sprintf('Content-Disposition: attachment; filename="%s"', $this->name));
	    if ($t > 0) {
	        header("HTTP/1.1 206 Partial content");
	        $t === 1 ? $this->pushSingle($range) : $this->pushMulti($ranges);
	    } else {
	        header("Content-Length: " . $this->size);
	        $this->readFile();
	    }
	    flush();
	}
	private function pushSingle($range) {
	    $start = $end = 0;
	    $this->getRange($range, $start, $end);
	    header("Content-Length: " . ($end - $start + 1));
	    header(sprintf("Content-Range: bytes %d-%d/%d", $start, $end, $this->size));
	    fseek($this->file, $start);
	    $this->readBuffer($end - $start + 1);
	    $this->readFile();
	}
	private function pushMulti($ranges) {
	    $length = $start = $end = 0;
	    $output = "";
	    $tl = "Content-type: " . $this->content_type . "\r\n";
	    $formatRange = "Content-range: bytes %d-%d/%d\r\n\r\n";
	    foreach ( $ranges as $range ) {
	        $this->getRange($range, $start, $end);
	        $length += strlen("\r\n--$this->boundary\r\n");
	        $length += strlen($tl);
	        $length += strlen(sprintf($formatRange, $start, $end, $this->size));
	        $length += $end - $start + 1;
	    }
	    $length += strlen("\r\n--$this->boundary--\r\n");
	    header("Content-Length: $length");
	    header("Content-Type: multipart/x-byteranges; boundary=$this->boundary");
	    foreach ( $ranges as $range ) {
	        $this->getRange($range, $start, $end);
	        echo "\r\n--$this->boundary\r\n";
	        echo $tl;
	        echo sprintf($formatRange, $start, $end, $this->size);
	        fseek($this->file, $start);
	        $this->readBuffer($end - $start + 1);
	    }
	    echo "\r\n--$this->boundary--\r\n";
	}
	private function getRange($range, &$start, &$end) {
	    list($start, $end) = explode('-', $range);
	    $fileSize = $this->size;
	    if ($start == '') {
	        $tmp = $end;
	        $end = $fileSize - 1;
	        $start = $fileSize - $tmp;
	        if ($start < 0)
	            $start = 0;
	    } else {
	        if ($end == '' || $end > $fileSize - 1)
	            $end = $fileSize - 1;
	    }
	    if ($start > $end) {
	        header("Status: 416 Requested range not satisfiable");
	        header("Content-Range: */" . $fileSize);
	        exit();
	    }
	    return array(
	            $start,
	            $end
	    );
	}
	private function readFile() {
	    while ( ! feof($this->file) ) {
	        echo fgets($this->file);
	        flush();
	        usleep($this->delay);
	    }
	}
	private function readBuffer($bytes, $size = 1024) {
	    $bytesLeft = $bytes;
	    while ( $bytesLeft > 0 && ! feof($this->file) ) {
	        $bytesLeft > $size ? $bytesRead = $size : $bytesRead = $bytesLeft;
	        $bytesLeft -= $bytesRead;
	        echo fread($this->file, $bytesRead);
	        flush();
	        usleep($this->delay);
	    }
	}
}



require_once('session.php');
session_write_close(); // don't block other scripts by locking the session file


if(isset($_GET['track'])) {
	// is a valid file path given?
	if(file_exists($_GET['track'])) {
		try {
			$download = new ResumeDownload($_GET['track'], "audio/mpeg", 0);
			$download->process();
		} catch (Exception $e) {
			header('HTTP/1.1 404 File Not Found');
			die('Sorry, an error occured.');
		}
		exit();
	}

	// otherwise search db for file path by track id
	require_once('database.php');
	$sql = "SELECT tr.id AS 'id', "
	     . "tr.title AS 'title', "
	     . "tr.path AS 'path', "
	     . "tr.track_number AS 'track_number', "
	     . "al.title AS 'album', "
	     . "al.id AS 'album_id', "
	     . "ar.title AS 'artist', "
	     . "tr.cover AS 'cover' "
	     . "FROM track tr "
	     . "INNER JOIN album al ON tr.album_id = al.id "
	     . "INNER JOIN artist ar ON tr.artist_id = ar.id "
	     . "WHERE tr.id = ?;";
	$statement = $mysqli->prepare($sql);
	$statement->bind_param('i', $_GET['track']);
	$statement->execute();
	$result = $statement->get_result();

	while($row = $result->fetch_object()) {
		$filename = $row->artist . " - " . $row->title . "." . pathinfo($row->path, PATHINFO_EXTENSION);
		if(isset($_GET['download']) && $_GET['download'] != "") {
			if(!ALLOW_DOWNLOADS) {
				header('HTTP/1.1 401 Forbidden');
				die('Download not allowed');
			}
			header("Content-Disposition: attachment; filename='$filename'");
			if(LOG_DOWNLOADS) {
				$sql = "INSERT INTO download (track_id, client) VALUES (?, ?)";
				$statement2 = $mysqli->prepare($sql);
				$statement2->bind_param('is', $row->id, $_SERVER['REMOTE_ADDR']);
				$statement2->execute();
			}
		}
		#readfile($row->path);

		try {
			$download = new ResumeDownload($row->path, "audio/mpeg", 0);
			$download->process();
		} catch (Exception $e) {
			header('HTTP/1.1 404 File Not Found');
			die('Sorry, an error occured.');
		}

		break;
	}

}

?>
