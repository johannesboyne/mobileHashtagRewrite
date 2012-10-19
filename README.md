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
location attribute. 

Let's start a little Web-Server:

``` js
http.createServer(function (req, res) {
	res.writeHead(200, {'Content-Type': 'text/plain'});

	res.write('127.0.0.1:8000\n\n');
	
	res.write('Headers:\n');
	res.write('--------\n');

	res.write(JSON.stringify(req.url)+'\n');
	res.write(JSON.stringify(req.headers));

	res.end('Hello World\n');
}).listen(8000, '127.0.0.1');
console.log('Server running at http://127.0.0.1:8000/');
```

Calling the URL `http://127.0.0.1:8000/#hash/blupp` gives the following output:

```
127.0.0.1:8000

Headers:
--------
"/"
{"host":"127.0.0.1:8000","connection":"keep-alive","cache-control":"max-age=0","user-agent":"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.94 Safari/537.4","accept":"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8","accept-encoding":"gzip,deflate,sdch","accept-language":"de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4","accept-charset":"ISO-8859-1,utf-8;q=0.7,*;q=0.3"}Hello World
```
Ouch! Yes, the hash is not transmitted to the server. "But I use Backbone.js and I need #####" (some guy).

Not a big problem, 99% of all modern browsers will accept a rewrite of the Location Header and keep the hash fragment. The 1% is the (iOS) Safari. 
Check it out by starting a different version of our Web-Server:

``` js
var http = require('http');
http.createServer(function (req, res) {
	res.writeHead(302, {
		'Location': 'http://127.0.0.1:8000'
	});

	res.end();
}).listen(9000, '127.0.0.1');
console.log('Server running at http://127.0.0.1:9000/');

http.createServer(function (req, res) {
	res.writeHead(200, {'Content-Type': 'text/plain'});

	res.write('127.0.0.1:8000\n\n');
	
	res.write('Headers:\n');
	res.write('--------\n');

	res.write(JSON.stringify(req.url)+'\n');
	res.write(JSON.stringify(req.headers));

	res.end('Hello World\n');
}).listen(8000, '127.0.0.1');
console.log('Server running at http://127.0.0.1:8000/');
``` 

Now call: `127.0.0.1:9000/#hash/blupp` (Chrome or Firefox will be best) and the output should be:

```
127.0.0.1:8000

Headers:
--------
"/"
{"host":"127.0.0.1:8000","user-agent":"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:16.0) Gecko/20100101 Firefox/16.0","accept":"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8","accept-language":"de-de,de;q=0.8,en-us;q=0.5,en;q=0.3","accept-encoding":"gzip, deflate","connection":"keep-alive"}Hello World
```
Open your JS Console and call `window.location.href` it will output: `"http://127.0.0.1:8000/#hash/blupp"`. Voila the `#hash/blupp` is present.

Now repeat it with Safari and look at the address bar `http://127.0.0.1:8000` **/#hash/blupp IS GONE!**.

__**What is the solution?**__

Everything except (iOS) Safari browser will be redirected by changing the HTTP Location Header. If it is a Safari we wont redirect on the server, therefor we have a very little JS call. 

Let's conquer it
----------------

##The PHP solution
Using the `Mobile_Detect.php` Lib it is kind of easy:
``` php
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

This is our very little JS call.

``` js
if (window.clientInformation.userAgent.search(/(iPhone)|(iPad)|(iPod)|(Mobile)/g) != -1) {
	if (confirm('We do have a mobile site, do you want to use it?'))
		window.location.href = window.location.href.replace(/www/g, "m")
}
```