mobileHashtagRewrite
====================

**[IN PROGRESS!]**

Redirecting an URL is a major feature for web applications because different 
browsers such as mobile or a desktop browsers must be redirected to specific 
domains.

But rewriting hashtagged urls is not as easy as it seems to be particularly if 
your users are iOS Safari guys...

The problem is that the iOS Safari Browsers wont keep a URL hash fragment after 
an url rewrite. Look at the following example:

`http://www.anurl.com/#hash/blupp` shall be `http://m.anurl.com/#hash/blupp` for 
all mobile browsers.

You would love to use an HTTP Header Location Rewrite ;) for sure BUT be aware! 
iOS's Safari forgets everything after the SERVER NAME if you simple rewrite the 
kocation attribute.

Let's conquer it
----------------

##The PHP solution
Using the `Mobile_Detect.php` Lib it is kind of easy:
```php
<?php
//print_r($_SERVER); exit();

require_once "Mobile_Detect.php";
$detect			= new Mobile_Detect;
$deviceType		= ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
$OS 			= $_SERVER['HTTP_USER_AGENT'];
$fragmentFound	= stristr($_SERVER['REQUEST_URI'], '?') != "" ? true : false;
$fragment		= substr(preg_replace('(\?)', '\#', $_SERVER['REQUEST_URI']), 2);
$domain			= explode(".", $_SERVER['SERVER_NAME']);

unset($domain[0]);

if($deviceType == 'tablet' || $deviceType == 'phone') {
	// this work around is definitely not a very clean nor even a pretty
	// one BUT it will work for now(?)!
	// on the client side we are replacing
	// #vt/dashboard/overview/28
	// with
	// ?vt/dashboard/overview/28
	// for QR-Codes and in links, publicised by E-Mail...
	if (preg_match("/(iPhone)|(iPod)|(iPad)/i", $OS)) {
		if ($fragmentFound) header('Location: http://m.'.implode(".", $domain).'/'.$fragment);
	} else {
		if ($fragmentFound) header('Location: http://m.'.implode(".", $domain).'/'.$fragment);
	    else header('Location: http://m.'.implode(".", $domain));
	}
} else {
	if ($fragmentFound) header('Location: http://www.'.implode(".", $domain).'/'.$fragment);
}

?>
```

##The Node.js solution (coming soon)
(I am a Node.js-ler)

Le Client-Side
--------------
```js
if (window.clientInformation.userAgent.search(/(iPhone)|(iPad)|(iPod)|(Mobile)/g) != -1) {
	if (confirm('Wir haben auch eine mobile Seite, wollen Sie diese nutzen?'))
		window.location.href = window.location.href.replace(/www/g, "m")
}
```