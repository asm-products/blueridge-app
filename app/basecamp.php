<?php 
/* 
 * 
 * 
 * 
// urls for code and then token
https://launchpad.37signals.com/authorization/new?type=web_server&client_id=e391c424f7787e13c608bda67a22c2b121e50418&redirect_uri=http://blueridgeapp.com/auth
https://launchpad.37signals.com/authorization/token?type=web_server&client_id=e391c424f7787e13c608bda67a22c2b121e50418&redirect_uri=http://blueridgeapp.com/auth&client_secret=secrete&code=code


username: mospired
password: gscellC56

curl -u mospired:gscellC56 -H 'User-Agent: MyApp (yourname@example.com)' https://basecamp.com/2011887/api/v1/projects.json */

$userpass = 'mospired:gscellC56';
$url = 'https://basecamp.com/2011887/api/v1/people.json';

$ch = curl_init();

// set URL and other appropriate options
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT , true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //needed as the cert doesn't seem to be valid according to curl
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $userpass);
curl_setopt($ch, CURLOPT_USERAGENT, 'TestClient (joey1.rivera@gmail.com)');

// grab URL and pass it to the browser
$data = curl_exec($ch);

if(!$data){
	echo 'no work';
	var_dump(curl_getinfo($ch));
	var_dump(curl_error($ch));
}else{
	var_dump($data);
}
	
// close cURL resource, and free up system resources
curl_close($ch);