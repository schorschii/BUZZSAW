function obj(id) {
	return document.getElementById(id);
}

function ajaxRequest(objID, url, scrollto) {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (obj(objID) != null)
				obj(objID).innerHTML = this.responseText;
			if (!(scrollto == "" || scrollto === undefined))
				scrollTo('element_'+scrollto);
		}
	};
	xhttp.open("GET", url, true);
	xhttp.send();
}

function isSafari() {
	var ua = navigator.userAgent.toLowerCase();
	if (ua.indexOf('safari') != -1) {
		if (ua.indexOf('chrome') > -1) {
			return false;
		} else {
			return true;
		}
	}
}
