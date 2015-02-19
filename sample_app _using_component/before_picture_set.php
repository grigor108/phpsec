<?php


/**
	
 * PHPSEC
	
 * 
	
 * @author Paweł Wlizło
	
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

if(!isset($_SESSION)) 

    { 

        session_start(); 

    } 

$login=$_SESSION['login'];

echo'

<!DOCTYPE HTML>

<html>

  <head>

    <title>Logowanie obrazkowe użytkownika '.$login.'</title> 

    <meta charset="utf-8" />

	<style>

	.thumbnail {

    float: left;

    width: 200px;

    height: 170px;

    margin: 15px;

	}

	

	</style>

	</head>



  <body>



	Wybierz obrazek na którym będziesz ustawiał logowanie<br>';

	

	foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator('img',RecursiveDirectoryIterator::SKIP_DOTS| FilesystemIterator::UNIX_PATHS)) as $filename)

	{

        echo '	

		  	<form method="POST" action="picture_set.php">

			<div class =thumbnail>

				<img src="'.$filename.'" width="190" height=160">

				ilość punktów:

					<select name="tryb">

					<option value="1">1</option>

					<option value="2">2</option>

					<option value="3">3</option>

					<option value="4">4</option>

					<option value="5">5</option>

					</select>

				<input type="hidden" name="adres" value="'.$filename.'" />

				<input type="submit" value="Wybierz">

				</div>

				</form>';



	}

	echo'

  </body>

</html>

';

?>