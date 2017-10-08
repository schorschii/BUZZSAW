// this function disables the link functionality from each a-element
// and opens the new page via JS

// fix for iOS Safari, where WebApps opens the link in a new Safari Tab
// instead of the current WebApp windows

// currently not used because ios web apps do not run in the background

function fixLinksForWebApp() {
	var a=document.getElementsByTagName("a");
	for(var i=0;i<a.length;i++)
	{
		if (a[i].getAttribute("href") != "" && a[i].getAttribute("href") != "#") {
			a[i].onclick = function()
			{
				window.location = this.getAttribute("href");
				return false
			}
		}
	}
}
