<?php 
if($_SERVER['REQUEST_METHOD']=='GET'){
	echo file_get_contents("http://dev-api.blueridgeapp.com/services/basecamp");
}else if ($_SERVER['REQUEST_METHOD']=='POST'){

}

//echo '{"name":"basecamp","authUrl":"https:\/\/launchpad.37signals.com\/authorization\/new?client_id=cbc3f4cff1def7da310df4d74c7baaffa106772a&redirect_uri=http%3A%2F%2Fdev-www.blueridgeapp.com%2Fbasecamp&type=web_server"}';