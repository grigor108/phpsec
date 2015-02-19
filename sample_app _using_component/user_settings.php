<!doctype html>

<html>

<head>

<meta charset="utf-8">

</head>

<body>

<?php



/**

 * User settings file for PHPSEC

 * 

 * @author Grzegorz Olszewski <grzegorz@olszewski.in>

 * @license Copyright 2014 Judyta Hofman, Grzegorz Olszewski, Paweł Wlizło
	
	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at
	
	http://www.apache.org/licenses/LICENSE-2.0
	
	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.	

 */



require("functions.php");



error_reporting(config_error_reporting_level);

if(session_id() == '') {

    session_start();

	

}

date_default_timezone_set(config_default_timezone);

$user=get_user_data($_SESSION['login'],$_SESSION['password']);



if ($_GET['action']=='change')

{

	

	set_user_settings($user['id'],'auth_mechanism',$_POST['auth_mechanism']);

	set_user_settings($user['id'],'phone',$_POST['phone']);

	$user=get_user_data($_SESSION['login'],$_SESSION['password']);

}

if ($_GET['action']=='new_secret')

	{

		$time = floor(time() / 30);

		$g = new GoogleAuthenticator();

		$secret=$g->generateSecret();

		echo '<a href="';

		//echo $g->getURL('googlowy',$_SERVER['HTTP_HOST'],$secret); <- "proper" way of use doesn't work

		echo "https://www.google.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth://totp/PHPSEC:".$user['login']."@".$_SERVER['HTTP_HOST']."?secret=".$secret;

		echo '" target="_blank" rel = "noreferrer">LINK</a>';

		set_user_settings('gauth_secret',$secret);

	}

echo '

<form method="POST" action="user_settings.php?action=change">

<h1>Settings</h1>

<h3> Authentication method </h3><br>

 <select name="auth_mechanism">

  <option value="email" ';

  if ($user['auth_mechanism']=="email") echo "selected";

echo '>Email</option>



	<option value="sms"';

	if ($user['auth_mechanism']=="sms") echo "selected";

	echo '>SMS</option>

	<option value="image"';

	if ($user['auth_mechanism']=="image") echo "selected";

	echo '>Image password</option>

	<option value="gauth"';

	if ($user['auth_mechanism']=="gauth") echo "selected";

	echo '>Google authenticator</option>

	</select> 

<h3> Additional info </h3><br>

	Phone (with country code (e.g. +44736525532): <input type="text" name="phone" value="'.$user['phone'].'" size="45"><br>

	<a href="user_settings.php?action=new_secret" target="_blank"> Generate new Google Authenticator Secret to pair with your app</a>



	<br>

<input type="submit" value="SAVE">

</form>

';



?>

