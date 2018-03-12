	function download(url) {
		obj('download_frame').src = url;
	}

	function openFile() {
		obj('file-input').click();
	}

/* global variables */
var visualizerOn = false;
var remotePlayerId = -1;
var currentTrackId = -1;
var mobileWidth = 780;
var mobileHeight = 500;
var isVolumeOpen = false;
var isMenuOpen = false;
var isCurrentPlaylistOpen = false;
var playMode = 0;

/* toggleMenu()
   toggleCurrentPlaylist()
   opens or closes the library (menu), current playlist or volume control */
function toggleMenu(scrollto) {
	var libparam_scrollto = "";
	if (scrollto != "") libparam_scrollto = "?scrollto_active=1";
	if (!isMenuOpen) {
		if (window.innerWidth < mobileWidth || window.innerHeight < mobileHeight) {
			obj('controls').style.display = "none";
			obj('controlbar').style.display = "none";
		}
		ajaxRequest('content','library.php'+libparam_scrollto, scrollto);
		obj('content').style.display = "block";
		obj('currentPlaylist').style.display = "none";
		obj('logo').classList.add('blur');
		obj('visualization').classList.add('blur');
		obj('mainPlayer').classList.add('blur');
		addClass(obj('contentbg'), 'active');
		isCurrentPlaylistOpen = false;
	} else {
		obj('logo').classList.remove('blur');
		obj('visualization').classList.remove('blur');
		obj('mainPlayer').classList.remove('blur');
		obj('content').innerHTML = "";
		obj('content').style.display = "none";
		obj('controls').style.display = "block";
		obj('controlbar').style.display = "table";
		removeClass(obj('contentbg'), 'active');
	}
	isMenuOpen = !isMenuOpen;
}
function toggleCurrentPlaylist() {
	obj('content').innerHTML = "";
	obj('content').style.display = "none";
	if (!isCurrentPlaylistOpen) {
		if (window.innerWidth < mobileWidth || window.innerHeight < mobileHeight) {
			obj('controls').style.display = "none";
			obj('controlbar').style.display = "none";
		}
		obj('currentPlaylist').style.display = "block";
		obj('logo').classList.add('blur');
		obj('visualization').classList.add('blur');
		obj('mainPlayer').classList.add('blur');
		addClass(obj('contentbg'), 'active');
		isMenuOpen = false;
	} else {
		obj('logo').classList.remove('blur');
		obj('visualization').classList.remove('blur');
		obj('mainPlayer').classList.remove('blur');
		obj('currentPlaylist').style.display = "none";
		obj('controls').style.display = "block";
		obj('controlbar').style.display = "table";
		removeClass(obj('contentbg'), 'active');
	}
	isCurrentPlaylistOpen = !isCurrentPlaylistOpen;
}

/* toggleFullscreen()
   self-explaining - on fullscreen button click */
function toggleFullscreen(elem) {
	elem = elem || document.documentElement;
	if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
		if (elem.requestFullscreen)
			elem.requestFullscreen();
		else if (elem.msRequestFullscreen)
			elem.msRequestFullscreen();
		else if (elem.mozRequestFullScreen)
			elem.mozRequestFullScreen();
		else if (elem.webkitRequestFullscreen)
			elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
	} else {
		if (document.exitFullscreen)
			document.exitFullscreen();
		else if (document.msExitFullscreen)
			document.msExitFullscreen();
		else if (document.mozCancelFullScreen)
			document.mozCancelFullScreen();
		else if (document.webkitExitFullscreen)
			document.webkitExitFullscreen();
	}
}

/* setPlayMode()
   set the play mode */
function setPlayMode(newPlayMode) {
	playMode = newPlayMode;
}

/* refreshPlayPauseButton()
   updates the image on the play/pause button depending on the player state */
function refreshPlayPauseButton() {
	if (!obj('mainPlayer').paused) {
		if(obj('btnPlayPause') != null)
			obj('btnPlayPause').style.backgroundImage = "url(img/pause.svg)";
		if(remotePlayerId != -1)
			ajaxRequest('dummy', 'remoteplayer.php?set='+remotePlayerId+'&state=1', '');
	}
	else {
		obj('btnPlayPause').style.backgroundImage = "url(img/play.svg)";
		if(remotePlayerId != -1)
			ajaxRequest('dummy', 'remoteplayer.php?set='+remotePlayerId+'&state=0', '');
	}
}

/* togglePlayPause()
   self-explaining */
function togglePlayPause() {
	if (obj('mainPlayer').paused) {
		if(isSafari() == true) 	obj('mainPlayer').play();
		else 					fadeAudioIn('mainPlayer');
		obj('btnPlayPause').style.backgroundImage = "url(img/pause.svg)";
	} else {
		if(isSafari() == true) 	obj('mainPlayer').pause();
		else 					fadeAudioOut('mainPlayer', true);
		obj('btnPlayPause').style.backgroundImage = "url(img/play.svg)";
	}
}

/* getTime()
   updates the time labels and time bar position */
var refreshTimeBar = true;
var last_ajax_time_update = -1;
function getTime() {
	// get current times from player object
	currentTime = obj('mainPlayer').currentTime;
	totalTime = obj('mainPlayer').duration;

	// calc percentage
	var percent = currentTime / totalTime * 100;

	// set values
	obj("timeBarCurrent").max = totalTime;
	if (refreshTimeBar) obj("timeBarCurrent").value = currentTime;
	obj("timeBarCurrentIndicator").style.width = percent + "%";

	// calc minutes and seconds from totalTime
	totalTime = totalTime.toFixed(0);
	secondsTotal = totalTime % 60;
	minutesTotal = (totalTime - secondsTotal) / 60;

	// calc minutes and seconds from currentTime
	currentTime = currentTime.toFixed(0);
	seconds = currentTime % 60;
	minutes = (currentTime - seconds) / 60;

	// add leading zero if necessary
	if (secondsTotal < 10) secondsTotal = "0" + secondsTotal;
	if (minutesTotal < 10) minutesTotal = "0" + minutesTotal;
	if (seconds < 10) seconds = "0" + seconds;
	if (minutes < 10) minutes = "0" + minutes;

	// set values for totalTime
	if (isNaN(minutesTotal) && isNaN(secondsTotal)) {
		obj('timeMinutesTotal').innerHTML = "─ ─ ";
		obj('timeSecondsTotal').innerHTML = "─ ─";
	} else {
		obj('timeMinutesTotal').innerHTML = minutesTotal;
		obj('timeSecondsTotal').innerHTML = secondsTotal;
	}

	// set values for currentTime
	obj('timeMinutes').innerHTML = minutes;
	obj('timeSeconds').innerHTML = seconds;

	// update remote player position
	if(remotePlayerId != -1) {
		// only update when 2 seconds are elapsed
		if(Math.abs(last_ajax_time_update - currentTime) > 2) {
			ajaxRequest('dummy', 'remoteplayer.php?set='+remotePlayerId+'&position='+Math.round(currentTime), '');
			last_ajax_time_update = Math.round(currentTime);
		}
	}
}

/* setTime()
   setVolume()
   updates the time/volume on the player object after time/volume slider was moved */
function setTime() {
	obj('mainPlayer').currentTime = obj("timeBarCurrent").value;
}
function setVolume() {
	obj('mainPlayer').volume = obj('volumeBar').value;
	// save volume in php session, so it can be restored when opening a new track
	ajaxRequest('dummy', 'sessionvars.php?set=volume&value='+obj('volumeBar').value, '');
}

// don't toggle play pause on space bar key press if prev/play/pause/next button is focused (this will call togglePlayPause() twice)
var controlButton_hasFocus = false;
function controlButton_onFocus() { controlButton_hasFocus=true; }
function controlButton_lostFocus() { controlButton_hasFocus=false; }
// toggle play/pause on space key down
window.addEventListener("keydown",function (e) {
	// 32 = SPACE
	if (e.keyCode == 32 && controlButton_hasFocus == false) {
		togglePlayPause();
	}
});

function thisindex(elm)
{
	var nodes = elm.parentNode.childNodes, node;
	var i = count = 0;
	while( (node=nodes.item(i++)) && node!=elm )
		if( node.nodeType==1 ) count++;
	return count;
}

var current = 0;
function initPlaylist(initialTrackNumber) {
	current = initialTrackNumber;
	var tracks = obj('playlist').getElementsByClassName('track');
	var index;
	for (index = 0; index < tracks.length; ++index) {
		tracks[index].addEventListener('click',function(e) {
			e.preventDefault();
			runPlaylist(this, "");
			current = thisindex(this.parentElement);
		});
	}

	obj('mainPlayer').addEventListener('ended',function(e) {
		nextTrack(true);
	});
}
function nextTrack(forward) {
	var tracks = playlist.getElementsByClassName('track');
	var len = tracks.length;

	if (playMode == 3) {
		current = Math.floor((Math.random() * len) + 0);
	} else if (playMode != 1) {
		// count up/down if play mode is not "repeat single track"
		if (forward) current++;
		else current--;
	}

	if(playMode == 1) {
		// play same track again
		link = tracks[current];
		runPlaylist(link, "");
	} else if(current < 0) {
		// first track and prev clicked, jump to last track in playlist
		link = tracks[len-1];
		runPlaylist(link, "");
	} else if(current == len && playMode == 0) {
		// end of current playlist, jump to first element
		current = 0;
		link = tracks[current];
		runPlaylist(link, "");
	} else if(current == len && playMode == 2) {
		// end of playlist, do not repeat playlist
		current--;
		player.pause();
		player.currentTime = 0;
	} else {
		// jump to next element in playlist
		link = tracks[current];
		runPlaylist(link, "");
	}
}
var isVideo = false;
function setVideoStyle(playerobjid) {
	if (playerobjid == "") player = obj('mainPlayer');
	else player = obj(playerobjid);
	if (player.videoHeight == 0 || player.videoHeight == null) {
		visualizerOn = true;
		isVideo = false;
		removeClass(player, 'videoactive');
		if(obj('bottombar') != null) removeClass(obj('bottombar'), 'videoactive');
		if(obj('menu') != null) removeClass(obj('menu'), 'videoactive');
	} else {
		visualizerOn = false;
		isVideo = true;
		addClass(player, 'videoactive');
		if(obj('bottombar') != null) addClass(obj('bottombar'), 'videoactive');
		if(obj('menu') != null) addClass(obj('menu'), 'videoactive');
	}
}

/* runPlaylist(link, playerobjid)
   starts playing a new track from the current playlist
   - link : html anchor object
   - optional playerobjid : the id of the player object to play this track
*/
function runPlaylist(link, playerobjid) {
	// get player object by id
	if (playerobjid == "") player = obj('mainPlayer');
	else player = obj(playerobjid);

	// set new player source
	player.src = link['href'];

	// set new artist, title, album text and load new cover image
	obj('titleArtist').innerHTML = link.getAttribute('titleArtist');
	obj('titleTitle').innerHTML = link.getAttribute('titleTitle');
	if (obj('titleAlbumDetails') != null)
		obj('titleAlbumDetails').innerHTML = link.getAttribute('titleAlbum');
	ajaxRequest('albumimg','getcover.php?title='+link.getAttribute('titleID'),'');
	ajaxRequest('albumimgDetails','getcover.php?title='+link.getAttribute('titleID'),'');
	// set new document title
	document.title = link.getAttribute('titleTitle') + " - " + link.getAttribute('titleArtist');
	// set new track id
	currentTrackId = link.getAttribute('titleID');
	// set new url
	let newURL = "player.php" + "?";
	let newURLparams = [];
	newURLparams.push(getQueryVariable("currentplaylist"));
	newURLparams.push(getQueryVariable("playlist"));
	newURLparams.push("track="+link.getAttribute('titleID'));
	newURL += newURLparams.join("&");
	window.history.pushState({}, null, newURL);

	// highlight new track in current playlist by setting style class
	var tracks = playlist.getElementsByClassName('track');
	var len = tracks.length - 1;
	for (index = 0; index < tracks.length; ++index)
		{ removeClass(tracks[index], 'active'); }
	addClass(link, 'active');

	// set audio volume to last state
	player.volume = audioLastState;

	// start playback
	player.load(); player.play(); setVolume();

	// if remote player is set, refresh current state
	if(remotePlayerId != -1)
		ajaxRequest('dummy', 'remoteplayer.php?set='+remotePlayerId+'&track='+link.getAttribute('titleID'), '');
}
function getQueryVariable(variable) {
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for (var i=0;i<vars.length;i++) {
		var pair = vars[i].split("=");
		if (pair[0] == variable) {
			return vars[i];
		}
	}
	return "";
}

/* fadeAudioOut(audioObjId, pause)
   fadeAudioIn(audioObjId)
   functions for fading audio in and out
   - pause : (boolean) pause player after faded out
   - playerobjid : the id of the player object to play this track
*/
var audioFadeStep = 0.1;
var audioLastState = 0.75;
function fadeAudioOut(audioObjId, pause) {
	var audioObj = obj(audioObjId);
	audioLastState = audioObj.volume;
	var fadeAudio = setInterval(function () {
		if(audioObj.volume - audioFadeStep < 0)
			audioObj.volume = 0;
		else
			audioObj.volume -= audioFadeStep;
		if (audioObj.volume === 0.0) {
			if(pause) audioObj.pause();
			clearInterval(fadeAudio);
		}
	}, 40);
}
function fadeAudioIn(audioObjId) {
	var audioObj = obj(audioObjId);
	audioObj.play();
	var fadeAudio = setInterval(function () {
		if(audioObj.volume + audioFadeStep > audioLastState)
			audioObj.volume = audioLastState;
		else
			audioObj.volume += audioFadeStep;
		if (audioObj.volume === audioLastState) {
			clearInterval(fadeAudio);
		}
	}, 40);
}

/* function for hiding the mouse when a video is playing */
function showCursor() {
	document.body.style.cursor = "default";
	obj('bottombar').style.opacity = "1";
	obj('menu').style.opacity = "1";
}
function hideCursor() {
	document.body.style.cursor = "none";
	obj('bottombar').style.opacity = "0";
	obj('menu').style.opacity = "0";
}
var hideCursorTime = 3000;
var hideCursorTimerFunction = function () {
	if(isVideo) {
		hideCursor();
	}
}
var hideCursorTimer  = setTimeout(hideCursorTimerFunction, hideCursorTime);
window.onmousemove = function() {
	showCursor();
	clearTimeout(hideCursorTimer);
	hideCursorTimer = setTimeout(hideCursorTimerFunction, hideCursorTime);
};

/* remote player functions */
function setRemotePlayer(select) {
	var selectedString = select.options[select.selectedIndex].value;
	remotePlayerId = selectedString;
}
function sendAllRemotePlayerParameters() {
	if(remotePlayerId != -1) {
		currentTime = obj('mainPlayer').currentTime;
		var currentstate = 1;
		if(obj('mainPlayer').paused) currentstate = 0;
		ajaxRequest('dummy', 'remoteplayer.php?set='+remotePlayerId+'&track='+currentTrackId+'&position='+Math.round(currentTime)+'&state='+currentstate, '');
		last_ajax_time_update = Math.round(currentTime);
	}
	ajaxRequest('dummy', 'remoteplayer.php?set_remoteplayer_session='+remotePlayerId,'');
}

/* helper functions for setting and removing style classes from objects */
function hasClass(el, className) {
	if (el.classList)
		return el.classList.contains(className)
	else
		return !!el.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'))
}
function addClass(el, className) {
	if (el.classList)
		el.classList.add(className)
	else if (!hasClass(el, className)) el.className += " " + className
}
function removeClass(el, className) {
	if (el.classList)
		el.classList.remove(className)
	else if (hasClass(el, className)) {
		var reg = new RegExp('(\\s|^)' + className + '(\\s|$)')
		el.className=el.className.replace(reg, ' ')
	}
}
