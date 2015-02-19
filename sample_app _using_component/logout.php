<?php

/**

 * PHPSEC example - Logout

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

?>

<!doctype html>

<html>

<head>

<title>Logout</title>

<meta charset="utf-8">

</head>

<body>

<?php

require_once('functions.php');



logout();

?>

<b>You have been logged out.</b>

<a href="login.php"><button>Ok</button></a>

</body>

</html>