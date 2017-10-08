<?php

/* functions for detecting browsers and operating systems */

function getOS1() {
	$ua = $_SERVER["HTTP_USER_AGENT"];
	if(strpos($ua, 'Android'))
		return "Android";
	elseif(strpos($ua, 'BlackBerry'))
		return "BlackBerry";
	elseif(strpos($ua, 'iPhone') || strpos($ua, 'iPad'))
		return "iPhone";
	elseif(strpos($ua, 'Palm'))
		return "Palm";
	elseif(strpos($ua, 'Linux'))
		return "Linux";
	elseif(strpos($ua, 'Macintosh'))
		return "Macintosh";
	elseif(strpos($ua, 'Windows'))
		return "Windows";
	else
		return "Unknown";
}

function getBrowser1() {
	$ua = $_SERVER["HTTP_USER_AGENT"];
	if(strpos($ua, 'Chrome'))
		return "Chrome";
	elseif(strpos($ua, 'Firefox'))
		return "Firefox";
	elseif(strpos($ua, 'MSIE'))
		return "InternetExplorer";
	elseif(preg_match("/\bOpera\b/i", $ua))
		return "Opera";
	elseif(strpos($ua, 'Safari'))
		return "Safari";
	else
		return "Unknown";
}

/*** for detecting different versions
$msie_7         = strpos($ua, 'MSIE 7.0') ? true : false;
$msie_8         = strpos($ua, 'MSIE 8.0') ? true : false;
$firefox_2      = strpos($ua, 'Firefox/2.0') ? true : false;
$firefox_3      = strpos($ua, 'Firefox/3.0') ? true : false;
$firefox_3_6    = strpos($ua, 'Firefox/3.6') ? true : false;
$safari_2       = strpos($ua, 'Safari/419') ? true : false;    // Safari 2
$safari_3       = strpos($ua, 'Safari/525') ? true : false;    // Safari 3
$safari_3_1     = strpos($ua, 'Safari/528') ? true : false;    // Safari 3.1
$safari_4       = strpos($ua, 'Safari/531') ? true : false;    // Safari 4
*/

?>
