<?php



/**

 * PHPSEC - Step 2 of authentication

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



error_reporting($config['error_reporting_level']);



SWITCH ($_SESSION['auth_mechanism'])

{

	case 'email':

		if (verify_user_data($_SESSION['login'],$_SESSION['password'],$_SESSION['$auth_mechanism'],$_POST['current_code'])==true)

		{

			$user=get_user_data($login,$password);



			$_SESSION['email'] = $email;

			$_SESSION['logged']=true;

			header("Location: loggedin.php");

		}

		else die ("You provided invalid authorisation code");

	break;

	case 'canvas':

		if (verify_user_data($_SESSION['login'],$_SESSION['password'],$_SESSION['$auth_mechanism'],$_POST['current_code'])==true)

		{

			$user=get_user_data($login,$password);



			$_SESSION['email'] = $email;

			$_SESSION['logged']=true;

			header("Location: loggedin.php");

		}

		else die ("You provided invalid authorisation code");

	break;

	case 'sms':

		if (verify_user_data($_SESSION['login'],$_SESSION['password'],$_SESSION['$auth_mechanism'],$_POST['current_code'])==true)

		{

			$user=get_user_data($login,$password);



			$_SESSION['email'] = $email;

			$_SESSION['logged']=true;

			header("Location: loggedin.php");

		}

		else die ("You provided invalid authorisation code");

	break;

	case 'gauth':

		if (verify_user_data($_SESSION['login'],$_SESSION['password'],$_SESSION['$auth_mechanism'],$_POST['current_code'])==true)

		{

			$user=get_user_data($login,$password);

			

			$_SESSION['email'] = $email;

			$_SESSION['logged']=true;

			header("Location: loggedin.php");

		}

		else die ("You provided invalid authorisation code");

	break;

}

?>

