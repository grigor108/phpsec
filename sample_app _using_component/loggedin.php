<?php

/**

 * PHPSEC example - success file

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



if ($_SESSION['logged']!=true) header("Location: login.php");

if (!isset($_SESSION['check']))
{
 	session_regenerate_id();
	$_SESSION['check'] = true;
	$_SESSION['uid'] = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);  //Why not only IP? Attacker can be on the same LAN as victim - IP could be the same checking also user agent string makes it harder
}

if ($_SESSION['uid'] !== md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']))
{
	echo 'Session hijacking detected';
	exit;
}

echo '<h1><b>I am logged in</b></h1><br>

	<a href="user_settings.php"><button>Account settings</button></a>

	<a href="logout.php"><button>Log out</button></a>';



?>

