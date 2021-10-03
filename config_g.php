<?php
		if(!session_id()){
			session_start();
		}
	require_once "GoogleAPI/vendor/autoload.php";
	$gClient = new Google_Client();
	$gClient->setClientId("1818070147-pt5u66hmcd38s815qj73k1e02rm5pil6.apps.googleusercontent.com");
	$gClient->setClientSecret("zXJGXV4eN8JUhuQoQJsf6TuH");
	$gClient->setApplicationName("lice");
	$gClient->setRedirectUri("http://localhost/lic/g-callback.php");
	$gClient->addScope("https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile");
?>