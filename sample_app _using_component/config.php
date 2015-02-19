<?php

/**

 * Config file for PHPSEC

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



//database config below

define ('config_dbhost', 'localhost', true); 

define ('config_dbname', 'change_me', true);	//your database name here

define ('config_dbusername', 'change_me', true); //database username

define ('config_dbpassword', 'your_password', true); //database password

define ('config_dbprefix', '', true); //database tables prefix here

 

require_once('functions.php'); //required to properly execute settings-reading function below

 

//global settings

define ('config_error_reporting_level', read_settings('config_error_reporting_level'), true);

define ('config_default_timezone', read_settings('config_default_timezone'), true);



//mailer config below

define ('config_mail_from', read_settings('config_mail_from'), true);

define ('config_mail_from_name', read_settings('config_mail_from_name'), true);

define ('config_mail_reply_to', read_settings('config_mail_reply_to'), true);

define ('config_mail_reply_to_name', read_settings('config_mail_reply_to_name'), true);

define ('config_mail_subject', read_settings('config_mail_subject'), true);



//sms config below

define ('config_sms_api', read_settings('config_sms_api'), true);

?>