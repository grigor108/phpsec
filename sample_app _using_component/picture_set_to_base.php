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



//$_POST['login']=$login;

$login = $_POST['login'];

//$_POST['tryb']=$tryb;

$tryb = $_POST['tryb'];

$picture_name = $_POST['picture'];



$sql_ktory_obrazek ='select count(*) from user_graph where id_users = (select id from users where login ="'.$login.'")'; // jesli uzytkownik ma kilka obrazkow, to jest ich numeracja

function ok()

{



	header('location:login.php');

	echo 'ustawione';

}

try{

	$pdo = new PDO('mysql:host='.config_dbhost.';dbname='.config_dbname.';charset=utf8', config_dbusername, config_dbpassword);

	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



	switch($tryb)

	{

		case 1:

		$p1x=$_POST['p1x'];

		$p1y=$_POST['p1y'];

		

		$stmt = $pdo-> query($sql_ktory_obrazek); // jesli uzytkownik ma kilka obrazkow, to jest ich numeracja

		$ktory_obrazek = $stmt -> fetchColumn();

		$ktory_obrazek=$ktory_obrazek+1;



		$stmt = $pdo->exec('

			insert into graph_point (id_graph_point,p1x,p1y) values (default,'.$p1x.','.$p1y.');

			insert into user_graph (id_user_graph,id_users,number_of_ug,tryb,id_graph_point,picture_name) values (

			default,

			(select id from users where login ="'.$login.'"),

			'.$ktory_obrazek.',

			'.$tryb.',

			(SELECT LAST_INSERT_ID()),

			"'.$picture_name.'");

			');

	 	;

		ok();

		$stmt -> closeCursor();

		break;

		case 2:

		$p1x=$_POST['p1x'];

		$p1y=$_POST['p1y'];

		$p2x=$_POST['p2x'];

		$p2y=$_POST['p2y'];

		

		$stmt = $pdo-> query($sql_ktory_obrazek); // jesli uzytkownik ma kilka obrazkow, to jest ich numeracja

		$ktory_obrazek = $stmt -> fetchColumn();

		$ktory_obrazek=$ktory_obrazek+1;

		

		$stmt = $pdo->exec('

			insert into graph_point (id_graph_point,p1x,p1y,p2x,p2y) values (default,'.$p1x.','.$p1y.','.$p2x.','.$p2y.');

			insert into user_graph (id_user_graph,id_users,number_of_ug,tryb,id_graph_point,picture_name) values (

			default,

			(select id from users where login ="'.$login.'"),

			'.$ktory_obrazek.',

			'.$tryb.',

			(SELECT LAST_INSERT_ID()),

			"'.$picture_name.'");

			');

			ok();

			$stmt -> closeCursor();

		break;

		

		case 3:

		$p1x=$_POST['p1x'];

		$p1y=$_POST['p1y'];

		$p2x=$_POST['p2x'];

		$p2y=$_POST['p2y'];

		$p3x=$_POST['p3x'];

		$p3y=$_POST['p3y'];

		

		$stmt = $pdo-> query($sql_ktory_obrazek); // jesli uzytkownik ma kilka obrazkow, to jest ich numeracja

		$ktory_obrazek = $stmt -> fetchColumn();

		$ktory_obrazek=$ktory_obrazek+1;

		

		$stmt = $pdo->exec('

			insert into graph_point (id_graph_point,p1x,p1y,p2x,p2y,p3x,p3y) values (default,'.$p1x.','.$p1y.','.$p2x.','.$p2y.','.$p3x.','.$p3y.');

			insert into user_graph (id_user_graph,id_users,number_of_ug,tryb,id_graph_point,picture_name) values (

			default,

			(select id from users where login ="'.$login.'"),

			'.$ktory_obrazek.',

			'.$tryb.',

			(SELECT LAST_INSERT_ID()),

			"'.$picture_name.'");

			');

			ok();

			$stmt -> closeCursor();

		break;

		

		case 4:

		$p1x=$_POST['p1x'];

		$p1y=$_POST['p1y'];

		$p2x=$_POST['p2x'];

		$p2y=$_POST['p2y'];

		$p3x=$_POST['p3x'];

		$p3y=$_POST['p3y'];

		$p4x=$_POST['p4x'];

		$p4y=$_POST['p4y'];

		

		$stmt = $pdo-> query($sql_ktory_obrazek); // jesli uzytkownik ma kilka obrazkow, to jest ich numeracja

		$ktory_obrazek = $stmt -> fetchColumn();

		$ktory_obrazek=$ktory_obrazek+1;

		

		$stmt = $pdo->exec('

			insert into graph_point (id_graph_point,p1x,p1y,p2x,p2y,p3x,p3y,p4x,p4y) values (default,'.$p1x.','.$p1y.','.$p2x.','.$p2y.','.$p3x.','.$p3y.','.$p4x.','.$p4y.');

			insert into user_graph (id_user_graph,id_users,number_of_ug,tryb,id_graph_point,picture_name) values (

			default,

			(select id from users where login ="'.$login.'"),

			'.$ktory_obrazek.',

			'.$tryb.',

			(SELECT LAST_INSERT_ID()),

			"'.$picture_name.'");

			');

			ok();

			$stmt -> closeCursor();

		break;

		

		case 5:

		$p1x=$_POST['p1x'];

		$p1y=$_POST['p1y'];

		$p2x=$_POST['p2x'];

		$p2y=$_POST['p2y'];

		$p3x=$_POST['p3x'];

		$p3y=$_POST['p3y'];

		$p4x=$_POST['p4x'];

		$p4y=$_POST['p4y'];

		$p5x=$_POST['p5x'];

		$p5y=$_POST['p5y'];	

		

		$stmt = $pdo-> query($sql_ktory_obrazek); // jesli uzytkownik ma kilka obrazkow, to jest ich numeracja

		$ktory_obrazek = $stmt -> fetchColumn();

		$ktory_obrazek=$ktory_obrazek+1;

		

		$stmt = $pdo->exec('

			insert into graph_point (id_graph_point,p1x,p1y,p2x,p2y,p3x,p3y,p4x,p4y,p5x,p5y) values (default,'.$p1x.','.$p1y.','.$p2x.','.$p2y.','.$p3x.','.$p3y.','.$p4x.','.$p4y.','.$p5x.','.$p5y.');

			insert into user_graph (id_user_graph,id_users,number_of_ug,tryb,id_graph_point,picture_name) values (

			default,

			(select id from users where login ="'.$login.'"),

			'.$ktory_obrazek.',

			'.$tryb.',

			(SELECT LAST_INSERT_ID()),

			"'.$picture_name.'");

			');

			ok();

			$stmt -> closeCursor();

		break;

	}

	} 

	catch(PDOException $e) {

	echo 'ERROR: ' . $e->getMessage();

	}



?>