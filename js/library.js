var contextMenuTargetId = -1;
function openContextMenu(targetId) {
	contextMenuTargetId = targetId;
	obj('libraryContextMenu').style.display = "inline-block";
	obj('libraryContextMenu').style.top = event.clientY+"px";
	obj('libraryContextMenu').style.left = event.clientX+"px";
	return false;
}
function closeContextMenu() {
	contextMenuTargetId = -1;
	obj('libraryContextMenu').style.display = "none";
	obj('libraryContextMenuAddToPlaylist').selectedIndex = 0;
	return false;
}
function addToPlaylist(playlist_id) {
	ajaxRequest("notification", "playlistedit.php?title=" +contextMenuTargetId+ "&addtoplaylist=" +playlist_id);
	closeContextMenu();
	clearNotification();
}

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
	ajaxRequest("content","library.php?view=playlist_content&action=moveup&playlist=" + playlist_id + "&moveup_playlist_track=" + track_id);
}
function moveTrackDownPlaylist(playlist_id, track_id) {
	ajaxRequest("content","library.php?view=playlist_content&action=movedown&playlist=" + playlist_id + "&movedown_playlist_track=" + track_id);
}
function removeTrackFromPlaylist(playlist_id, track_id) {
	ajaxRequest("content","library.php?view=playlist_content&action=remove&playlist=" + playlist_id + "&remove_playlist_track=" + track_id);
}

var currentTimeout;
function clearNotification() {
	clearTimeout(currentTimeout);
	currentTimeout = setTimeout(function(){ obj("notification").innerHTML = ''; }, 2500);
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
