<?php

/**

 * PHPSEC example - Login engine

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



require_once('functions.php');

require_once('config.php');

error_reporting(config_error_reporting_level);

?>

<?php

$login = $_POST['login'];

$password = $_POST['password'];

$password = addslashes($password);

$login = addslashes($login);

$login = htmlspecialchars($login);

$last_login_ip = $_SERVER['REMOTE_ADDR'];

$last_login_date_time=date('d-m-Y H:i:s');



if (!empty($_GET['login'])) { //direct open safety cut-off

die();

}

if (!empty($_GET['password'])) {  //direct open safety cut-off

die();

}



$password = md5($password);



$user=get_user_data($login,$password);

$login=$user['login'];

$password=$user['password'];

$auth_mechanism=$user['auth_mechanism'];

session_start();

$_SESSION['login']=$login;

$_SESSION['password']=$password;



auth_step_1($login,$password,$auth_mechanism);

?>

