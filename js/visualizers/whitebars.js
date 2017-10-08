/*
	Visualizer: whitebars.js
	Author: Georg Sieber
	Version: 1.0

	like rainbowbars.js, but just white bars :-)
*/

var bars = 64;
var gap = 4;

function renderFrame() {
	if (audioPlayer.paused == false && visualizerOn == true) {
		var barWidth = (canvas.width / bars) - gap;
		var array = new Uint8Array(analyser.frequencyBinCount);
		analyser.getByteFrequencyData(array);
		var step = Math.round((array.length*0.75) / bars);
		draw.clearRect(0, 0, canvas.width, canvas.height);
		for (var i = 0; i < bars; i++) {
			var value = ((canvas.height/2)) * array[i * step] / 220;
			draw.fillStyle = "white";
			draw.fillRect((i*barWidth) + (i*gap),
						  (canvas.height/2) - (value/2),
						  barWidth,
						  value);
		}
	}
	requestAnimationFrame(renderFrame);
}
renderFrame();
