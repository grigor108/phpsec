<?php

/**

 * PHPSEC example - login page

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

//print_r($_SESSION['logged']);

if (isset($_SESSION['login']) AND ($_SESSION['password']))    

{  

$login = $_SESSION['login'];

$password = $_SESSION['password'];

$logged = $_SESSION['logged'];

header("Location: loggedin.php");

}

require_once('functions.php');

    if (empty($login) AND empty($password) AND empty($logged)) {



echo '<!doctype html>

<html>

<head>

<meta charset="utf-8">

<title>SuperSafe Login Form&trade;</title>

</head>



<body>';

        

        echo '

<div class="wrapper" align="center">



<table width="250" border="1" cellspacing="0" cellpadding="0">

  <tr>

    <td align="center">

    <br><br>





	<form action="login_engine.php" method="POST">

	<br>Login:<br>

	<input type="text" name="login">

	<br>Password:<br>

	<input type="password" name="password">

	<p><input type="submit" value="Log-In"></a></p><br/>

	<p></p><br>

	</form>

    </td>

  </tr>

</table>';





echo'

</div>

';





echo '</body>

</html>'; //message to not logged in users



exit;

}

else {

echo '<html>

<head>

<meta http-equiv="Refresh" content="0; url=loggedin.php" />

</head>

<body>



</body>

</html>';

}



$user=get_user_data($login,$password);

    if (empty($user['id']) OR !isset($user['id'])) {

echo '

<!doctype html>

<html>

<head>

<meta charset="utf-8">

<title>SuperSafe Login Form&trade;</title>

</head>



<body>';

        

        echo '

<div class="wrapper" align="center">



<table width="250" border="1" cellspacing="0" cellpadding="0">

  <tr>

    <td align="center">

    <br><br>

    



	<form action="login_engine.php" method="POST">

	<br>Login:<br>

	<input type="text" name="login">

	<br>Password:<br>

	<input type="password" name="password">

	<p><input type="submit" value="Log-In"></a></p><br/>

	<p></p><br>

	</form>

    </td>

  </tr>

</table>';





echo'

</div>

</body>

</html>';

exit;

}



?>

