<?php
/**
	
 * PHPSEC
	
 * 
	
 * @author Paweł Wlizlo
	
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

require("config.php");

if(!isset($_SESSION)) 

    { 

        session_start(); 

    }



$login= $_SESSION['login'];

$pdo = new PDO('mysql:host='.config_dbhost.';dbname='.config_dbname, config_dbusername, config_dbpassword);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try{

$stmt = $pdo-> query('select count(*) from user_graph where id_users = (select id from users where login ="'.$login.'")');

$result = $stmt -> fetchColumn();

$stmt -> closeCursor();

	}

	catch(PDOException $e) {

	echo 'ERROR: ' . $e->getMessage();

	}

	

if ($result != 0)

{

	try{

	$ilosc = $pdo -> query('select * from user_graph where id_users = (select id from users where login ="'.$login.'")');

	}

	catch(PDOException $e) {

	echo 'ERROR: ' . $e->getMessage();

	}

	echo'

	<!DOCTYPE HTML>

	<html>

	  <head>

		<title>Logowanie obrazkowe</title>

		<meta charset="utf-8" />

		<style>

		.thumbnail {

		float: left;

		width: 200px;

		height: 170px;

		margin: 15px;

		}

		.baton{

			padding:0px;

			background:none;

			border:none;

			margin:0px;

		}

		</style>

	  </head>



	  <body>

	  ';

		if (isset($_GET['alert'])) {

		echo'

		Nie trafiłeś. Wybierz ponownie<br>';}

		else{

		echo'

		Użytkownik '.$login.' ma zdefiniowane logowania obrazkowe <br>';}

		foreach ($ilosc as $row)

		{

		echo'

			<form method="POST" action="picture_check.php">

				<div class =thumbnail>

				<button type="submit" class="baton" value="Wybierz">

					<img src="'.$row["picture_name"].'" width="190" height=160">

					</button>

					<input type="hidden" name="id_user_graph" value="'.$row["id_user_graph"].'" />

					<input type="hidden" name="id_users" value="'.$row["id_users"].'" />

					<input type="hidden" name="tryb" value="'.$row["tryb"].'" />

					<input type="hidden" name="id_graph_point" value="'.$row["id_graph_point"].'" />

					<input type="hidden" name="adres" value="'.$row["picture_name"].'" />

					Obrazek nr: '.$row["number_of_ug"].'

					

				</div>

			</form>



		';

		}

		$ilosc -> closeCursor();

	  echo'			<form method="POST" action="before_picture_set.php">

					Czy chcesz dodać nowe logowanie obrazkowe?<br>

					<input type="submit" value="Dodaj">

					</form>

	  </body>

	</html>

  

';

}



else

{			

echo'

	<!DOCTYPE HTML>

	<html>

	  <head>

		<title>Logowanie obrazkowe</title>

		<meta charset="utf-8" />

		</head>



	  <body>

		Użytkownik '.$login.' nie ma jeszcze zdefiniowanego logowania obrazkowego. <br>

		<form method="POST" action="before_picture_set.php">

		Czy chcesz dodać nowe logowanie obrazkowe?<br>

		<input type="submit" value="Dodaj">

		</form>

	  </body>

	</html>

  

';}



?>