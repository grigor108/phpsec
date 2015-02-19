<!doctype html>

<html>

<head>

<meta charset="utf-8">

</head>

<body>

<?php



/**

 * Settings file file for PHPSEC

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

if ($_SESSION['logged']!=true) header("Location: login.php");

if ($_GET['action']=='change')

{

	

	set_settings('config_error_reporting_level',addslashes($_POST['config_error_reporting_level']));

	set_settings('config_default_timezone',addslashes($_POST['config_default_timezone']));

	set_settings('config_mail_from',addslashes($_POST['config_mail_from']));

	set_settings('config_mail_from_name',addslashes($_POST['config_mail_from_name']));

	set_settings('config_mail_reply_to',addslashes($_POST['config_mail_reply_to']));

	set_settings('config_mail_reply_to_name',addslashes($_POST['config_mail_reply_to_name']));

	set_settings('config_mail_subject',addslashes($_POST['config_mail_subject']));

	set_settings('config_sms_api',addslashes($_POST['config_sms_api']));

}

echo '

<form method="POST" action="settings.php?action=change">

<h1>Settings</h1>

<h3> Global settings </h3><br>

PHP error reporting level (e.g. E_ALL~E_NOTICE): <input type="text" name="config_error_reporting_level" value="'.stripslashes(read_settings('config_error_reporting_level')).'"><br>

Default timezone in PHP format (e.g. Europe/Berlin): <input type="text" name="config_default_timezone" value="'.stripslashes(read_settings('config_default_timezone')).'" size="30"><br>



<h3> Mail settings </h3><br>

	E-mail auth sender address (e.g. something@example.com): <input type="mail" name="config_mail_from" value="'.stripslashes(read_settings('config_mail_from')).'" size="45"><br>

	E-mail auth sender name: <input type="text" name="config_mail_from_name" value="'.stripslashes(read_settings('config_mail_from_name')).'" size="35"><br>

	E-mail auth reply-to address (e.g. reply@example.com): <input type="mail" name="config_mail_reply_to" value="'.stripslashes(read_settings('config_mail_reply_to')).'" size="45"><br>

	E-mail auth reply-to name:: <input type="text" name="config_mail_reply_to_name" value="'.stripslashes(read_settings('config_mail_reply_to_name')).'" size="35"><br>

	E-mail auth subject: <input type="text" name="config_mail_subject" value="'.stripslashes(read_settings('config_mail_subject')).'" size="85"><br>

	

<h3> SMS settings </h3><br>

SMS API URL: <input type="text" name="config_sms_api" value="'.stripslashes(read_settings('config_sms_api')).'" size="100"><br>



	<br>

<input type="submit" value="SAVE">

</form>

';



?>

