// get audio context (depends on engine)
window.AudioContext = window.AudioContext || window.webkitAudioContext || window.mozAudioContext;

// funtion resizeCanvas, called when browser window resized
function resizeCanvas() {
	var w = window,
	    d = document,
	    e = d.documentElement,
	    g = d.getElementsByTagName('body')[0],
	    x = w.innerWidth || e.clientWidth || g.clientWidth,
	    y = w.innerHeight|| e.clientHeight|| g.clientHeight;
	obj('visualizationCanvas').setAttribute('width', x*0.9);
}
window.onresize = function(event) {
	resizeCanvas();
};

// global variables, used by visualizer scripts
var audioPlayer;
var analyser;
var canvas;
var draw;

// load new visualizer
function setVisualizer(name) {
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = "js/visualizers/"+name;
	document.getElementsByTagName("head")[0].appendChild(script);
}

// initialize visualizer
window.onload = function() {
	audioPlayer = obj('mainPlayer');
	var audioContext = new AudioContext();
	analyser = audioContext.createAnalyser();
	var audioSrc = audioContext.createMediaElementSource(audioPlayer);
	audioSrc.connect(analyser);
	analyser.connect(audioContext.destination);
	canvas = obj('visualizationCanvas');
	draw = canvas.getContext('2d');
	resizeCanvas();

	// load default visualizer script
	setVisualizer('rainbowbars.js');
};
