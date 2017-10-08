var last_track_id = -1;

function ajaxRequestRemote(url) {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			/* params - php script returns one param per line:
			   0 - track id
			   1 - track url
			   2 - play state (0=pause, 1=play)
			   3 - track title
			   4 - current play position (seconds)
			   5 - album title
			   6 - artist
			*/
			var params = this.responseText.split("\n");
			if(params[0] != last_track_id) {
				player = obj('mainPlayer');
				player.src = params[1];
				obj('titleArtist').innerHTML = params[6];
				obj('titleTitle').innerHTML = params[3];
				document.title = params[3] + " - " + params[6];
				ajaxRequest('albumimg','getcover.php?title='+params[0],'');
				last_track_id = params[0];
				player.load();
				player.play();
			}
			player = obj('mainPlayer');
			if(params[2] == 0 && player.paused == false) {
				player.pause();
			}
			if(params[2] == 1 && player.paused == true) {
				player.play();
			}
			if(Math.abs(params[4] - obj('mainPlayer').currentTime) > 4) {
				obj('mainPlayer').currentTime = params[4];
			}
		}
	};
	xhttp.open("GET", url, true);
	xhttp.send();
}
