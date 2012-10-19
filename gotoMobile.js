if (window.clientInformation.userAgent.search(/(iPhone)|(iPad)|(iPod)|(Mobile)/g) != -1) {
	if (confirm('We do have a mobile site, do you want to use it?'))
		window.location.href = window.location.href.replace(/www/g, "m")
}