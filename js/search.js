function searchList(input, ul) {
	var input, filter, ul, li, a, i;
	input = document.getElementById(input);
	filter = input.value.toUpperCase();
	ul = document.getElementById(ul);
	li = ul.getElementsByTagName('li');

	// loop through all list items, and hide those who don't match the search query
	for (i = 0; i < li.length; i++) {
		a = li[i].getElementsByTagName("a")[0];
		if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
			li[i].style.display = "";
		} else {
			li[i].style.display = "none";
		}
	}
}
