/*
	Visualizer: rainbowbars.js
	Author: Georg Sieber
	Version: 1.0
*/

/*
	GLOBAL VARIABLES EXPLANATION FOR DEVELOPING YOUR OWN VISUALIZER
	audioPlayer - the HTML audio/video object
	analyser - the audio contect createAnalyser() object
	canvas - the HTML canvas object
	draw - the canvas.getContext("2d") object
	function renderFrame() - contains your visualizer render frame logic
	--> should contain if(audioPlayer.paused == false && visualizerOn == true) to ensure your visualisation is not running while paused or video is running
*/

var bars = 64;
var gap = 4;
var colors =
[
"#B40000",
"#C10000",
"#D60000",
"#F00000",
"#FF200B",
"#FF3900",
"#FF5200",
"#FF6C00",
"#FF8600",
"#FFA000",
"#FFB900",
"#FFD200",
"#FFEA00",
"#FFFF00",
"#FDFF00",
"#E6FF00",
"#CEFF00",
"#B5FF00",
"#9BFF00",
"#81FF00",
"#67FF00",
"#4EFF00",
"#35FF00",
"#1CFF00",
"#05FF00",
"#00FF00",
"#00FF14",
"#00FF2C",
"#00FF45",
"#00FF5E",
"#00FF78",
"#00FF92",
"#00FFAC",
"#00FFC5",
"#00F7DD",
"#00E0F5",
"#00C7FF",
"#00AEFF",
"#0094FF",
"#007AFF",
"#0060FF",
"#0047FF",
"#002EFF",
"#0016FF",
"#0000FF",
"#0000FF",
"#0300FF",
"#1A00FF",
"#3200FF",
"#4B00FF",
"#6500FF",
"#7F00FF",
"#9900FF",
"#B200FF",
"#CB00FF",
"#E400FF",
"#FB00FF",
"#FF00FF",
"#FF00EC",
"#FF00D4",
"#FF00BB",
"#FF00A2",
"#FF0088",
"#FF006E"
]

function hexToRgb(hex) {
	var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
	return result ? {
		r: parseInt(result[1], 16),
		g: parseInt(result[2], 16),
		b: parseInt(result[3], 16)
	} : null;
}

function renderFrame() {
	if (audioPlayer.paused == false && visualizerOn == true) {
		var barWidth = (canvas.width / bars) - gap;
		var array = new Uint8Array(analyser.frequencyBinCount);
		analyser.getByteFrequencyData(array);
		var step = Math.round((array.length*0.75) / bars);
		draw.clearRect(0, 0, canvas.width, canvas.height);
		for (var i = 0; i < bars; i++) {
			var value = ((canvas.height/2)) * array[i * step] / 220;
			draw.fillStyle = colors[i];
			draw.fillRect((i*barWidth) + (i*gap),
						  (canvas.height/2) - (value/2),
						  barWidth,
						  value);
		}
	}
	requestAnimationFrame(renderFrame);
}
renderFrame();
