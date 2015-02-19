<?php



/**

 * Functions file for PHPSEC

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



require("libs/phpmailer/PHPMailerAutoload.php");

require("libs/GoogleAuthenticator/GoogleAuthenticator.php");

require("config.php");



error_reporting(config_error_reporting_level);

if(session_id() == '') 
{

    session_start();
}
    
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

date_default_timezone_set(config_default_timezone);



function read_settings($setting) //reading passed setting value from db (must be exactly the same as column name in db)

{

	try 
	{
		$pdo = new PDO('mysql:host='.config_dbhost.';dbname='.config_dbname.';charset=utf8', config_dbusername, config_dbpassword);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $pdo->prepare('SELECT * from '.config_dbprefix.'phpsec_settings');

		$stmt->execute();

		$result = $stmt->fetchAll();

		$setting_value=$result[0][$setting];

	} 
	catch(PDOException $e)
	{

		echo 'ERROR: ' . $e->getMessage();

	}

	return $setting_value;

}



function save_settings($setting) //reading passed setting value from db (must be exactly the same as column name in db)

{

	try 
	{
		$pdo = new PDO('mysql:host='.config_dbhost.';dbname='.config_dbname.';charset=utf8', config_dbusername, config_dbpassword);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $pdo->prepare('SELECT * from '.config_dbprefix.'phpsec_settings');

		$stmt->execute();

		$result = $stmt->fetchAll();

		$setting_value=$result[0][$setting];

	} 
	catch(PDOException $e) 
	{
		echo 'ERROR: ' . $e->getMessage();

	}
	return $setting_value;

}





function set_settings($setting_name,$setting_value)

{

	try 
	{
		$pdo = new PDO('mysql:host='.config_dbhost.';dbname='.config_dbname.';charset=utf8', config_dbusername, config_dbpassword);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql='UPDATE `'.config_dbprefix.'phpsec_settings` SET `'.$setting_name.'` = :setting_value WHERE 1';

		$stmt = $pdo->prepare($sql);

	

		$stmt->execute(array (

		':setting_value'=>$setting_value

		));

	} 
	catch(PDOException $e)
	{

		echo 'Error: ' . $e->getMessage();

	}

	return true;

}



function set_user_settings($id,$setting_name,$setting_value)

{

	try 
	{


		$pdo = new PDO('mysql:host='.config_dbhost.';dbname='.config_dbname.';charset=utf8', config_dbusername, config_dbpassword);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql='UPDATE `'.config_dbprefix.'users` SET `'.$setting_name.'` = :setting_value WHERE `id`=:id';
		$stmt = $pdo->prepare($sql);

	
		$stmt->execute(array (

		':setting_value'=>$setting_value,

		':id'=>$id

		));


	} 
	catch(PDOException $e) 
	{
		echo 'Error: ' . $e->getMessage();

	}

	return true;

}



function get_user_data($login,$password)

{

	try 
	{
		$pdo = new PDO('mysql:host='.config_dbhost.';dbname='.config_dbname.';charset=utf8', config_dbusername, config_dbpassword);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	 $stmt = $pdo->prepare('SELECT * from '.config_dbprefix.'users WHERE login = :login AND password = :password LIMIT 1');

	 $stmt->execute(array(':login' => $login,

	 ':password' => $password));


	  $result = $stmt->fetchAll();

	$result_final=$result[0];

	}

	catch(PDOException $e) 
	{

	  echo 'Error: ' . $e->getMessage();

	}

	return $result_final;

}



function logout()

{

	//COMPLETE SESSION DESTRUCTION - NOT ONLY SESSION DATA

	// Unset all of the session variables.

	$_SESSION = array();



	// If it's desired to kill the session, also delete the session cookie.

	// Note: This will destroy the session, and not just the session data!

	if (ini_get("session.use_cookies")) 
	{

		$params = session_get_cookie_params();

		setcookie(session_name(), '', time() - 42000,

			$params["path"], $params["domain"],

			$params["secure"], $params["httponly"]

		);

	}

	// Finally, destroy the session.

	session_destroy();

}



function verify_user_data($login,$password,$auth_mechanism,$current_code)

{

	try {

			$pdo = new PDO('mysql:host='.config_dbhost.';dbname='.config_dbname.';charset=utf8', config_dbusername, config_dbpassword);
		
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			 $stmt = $pdo->prepare('SELECT * from '.config_dbprefix.'users WHERE login = :login AND password = :password LIMIT 1');

			 $stmt->execute(array(':login' => $login,

			 ':password' => $password));
		 

			  $result1 = $stmt->fetchAll();

			  $result=$result1[0];
 

			  //Google Authenticator check handling

			  $user=get_user_data($login,$password);

			  if ($user['auth_mechanism']=="gauth")

			  {

				$time = floor(time() / 30);

				$g = new GoogleAuthenticator();

				$secret=$user['gauth_secret'];

				if ($g->checkCode($secret,$current_code)) $gauth_pass=true;

				else $gauth_pass=false;

			  }
  

			  if($login=$result['login'] AND $password=$result['password'] AND $auth_mechanism=$result['auth_mechanism'] AND $current_code=$result['current_code']) $verification = true;

			  else $verification = false;

	}

	catch(PDOException $e) 
	{

		echo 'Error: ' . $e->getMessage();

	}

	return $verification;

}



function auth_step_1($login,$password,$auth_mechanism)

{

	try 
	{
		$pdo = new PDO('mysql:host='.config_dbhost.';dbname='.config_dbname.';charset=utf8', config_dbusername, config_dbpassword);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$user=get_user_data($login,$password);

			$email=$user['email'];

			$code=generate_new_code($login,$password);

			$time = floor(time() / 30);

			if (empty($user))
			{
				echo 'Wrong login or password. <a href="login.php">Go back</a>';
				session_destroy();
			}

			switch ($auth_mechanism)

			{

				case 'email':

					mailer($user['email'],$code);

					echo '<!doctype html>

					<html>

					<head>

					<meta charset="utf-8">

					<title>SuperSafe Login Form&trade;</title>

					</head>

					<body>';



					echo '

					<form method="POST" action="auth_step2.php">

					An email with verification code has been sent to your e-mail address ('.$user['email'].'). Please enter the code below:<br><br>

					<input type="text" name="current_code"><br>

					<input type="submit" value="Continue">

					</form>

					';

					session_start();

					$_SESSION['login']=$login;

					$_SESSION['password']=$password;

					$_SESSION['auth_mechanism']=$auth_mechanism;



				break;



				case 'image':

					session_start();

					$_SESSION['login']=$login;

					$_SESSION['password']=$password;

					$_SESSION['auth_mechanism']=$auth_mechanism;

					include ('graph_user.php');

				break;



				case 'sms':

					$apikey='khasphas';

					$number=$user['phone'];

					$ch = curl_init(config_sms_api."?apikey=".$apikey."&code=".$code."&number=".$number."");

					curl_exec($ch);

					curl_close($ch);

					echo '<!doctype html>

					<html>

					<head>

					<meta charset="utf-8">

					<title>SuperSafe Login Form&trade;</title>

					</head>

					<body>';



					echo '

					<form method="POST" action="auth_step2.php">

					An email with verification code has been sent to phone ('.$user['phone'].'). Please enter the code below:<br><br>

					<input type="text" name="current_code"><br>

					<input type="submit" value="Continue">

					</form>

					';

					session_start();

					$_SESSION['login']=$login;

					$_SESSION['password']=$password;

					$_SESSION['auth_mechanism']=$auth_mechanism;

				break;

					

				case 'gauth':

					echo '<!doctype html>

						<html>

						<head>

						<meta charset="utf-8">

						<title>SuperSafe Login Form&trade;</title>

						</head>

						<body>';

						

					echo '

						<form method="POST" action="auth_step2.php">

						Please check your Google Authenticator app and enter the code below:<br><br>

						<input type="text" name="current_code"><br>

						<input type="submit" value="Continue">

						</form>

						';

					session_start();

					$_SESSION['login']=$login;

					$_SESSION['password']=$password;

					$_SESSION['auth_mechanism']=$auth_mechanism;

						

				break;

			}



		}

	catch(PDOException $e) 
	{
	  echo 'Error: ' . $e->getMessage();
	}

	return $result;

}





function mailer ($recipient_email, $current_code)

{


	$mail = new PHPMailer;

	$mail->From = config_mail_from;

	$mail->FromName = config_mail_from_name;

	$mail->addAddress($recipient_email);     // Add a recipient

	$mail->addReplyTo(config_mail_reply_to, config_mail_reply_to_name);

	$mail->isHTML(true);                                  // Set email format to HTML


	$mail->Subject = config_mail_subject;

	$mail->Body    = '<b>'.$current_code.'</b>';

	$mail->AltBody = $current_code;



	if(!$mail->send()) 
	{
		echo 'Message could not be sent.';

		echo 'Mailer Error: ' . $mail->ErrorInfo;

	} 
	else 
	{

		echo 'Message has been sent';

	}

	  
}



function generate_new_code($login,$password)

{
	$pdo = new PDO('mysql:host='.config_dbhost.';dbname='.config_dbname.';charset=utf8', config_dbusername, config_dbpassword);
	
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$code=rand (1111,9999);

	  $pdo->query('SET NAMES utf8');

	 $stmt = $pdo->prepare('UPDATE '.config_dbprefix.'users SET `current_code`=:code WHERE login = :login AND password = :password');

	 $stmt->execute(array(':code' => $code,

	 ':login' => $login,

	 ':password' => $password

	 ));



	 return $code;

}

