function createPlaylist() {
	var txt = prompt("Please enter a name for the new playlist:", "");
	if (!(txt == null || txt == "")) {
		ajaxRequest("content","library.php?view=playlist&new_playlist_name=" + txt);
	}
}

function removePlaylist(id) {
	if (confirm("Are you sure?"))
		ajaxRequest("content","library.php?view=playlist&remove_playlist=" + id);
}

function moveTrackUpPlaylist(playlist_id, track_id) {
	ajaxRequest("content","library.php?view=playlist_content&playlist=" + playlist_id + "&moveup_playlist_track=" + track_id);
}
function moveTrackDownPlaylist(playlist_id, track_id) {
	ajaxRequest("content","library.php?view=playlist_content&playlist=" + playlist_id + "&movedown_playlist_track=" + track_id);
}
function removeTrackFromPlaylist(playlist_id, track_id) {
	ajaxRequest("content","library.php?view=playlist_content&playlist=" + playlist_id + "&remove_playlist_track=" + track_id);
}

function clearNotification() {
	setTimeout(function(){ obj("notification").innerHTML = ''; }, 2500);
}

function scrollTo(id) {
	var topPos = obj(id).offsetTop;
	obj('containerContainer').scrollTop = topPos;
}

// override search shortcut
window.addEventListener("keydown",function (e) {
	// 114 = F3
	// 70 = F
	if (e.keyCode === 114 || (e.ctrlKey && e.keyCode === 70)) {
		var searchBarObj = obj('search');
		if (searchBarObj != null) {
			e.preventDefault();
			searchBarObj.focus();
		}
	}
});
