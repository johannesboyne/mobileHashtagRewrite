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