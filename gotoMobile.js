if (window.clientInformation.userAgent.search(/(iPhone)|(iPad)|(iPod)|(Mobile)/g) != -1) {
	if (confirm('Wir haben auch eine mobile Seite, wollen Sie diese nutzen?'))
		window.location.href = window.location.href.replace(/www/g, "m")
}