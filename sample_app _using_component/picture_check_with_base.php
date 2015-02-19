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

	

if(!isset($_SESSION['login']))

{

	$_SESSION['login']=$_POST['login'];

}

	

$id_user_graph = $_POST['id_user_graph'];

$id_graph_point = $_POST['id_graph_point'];

$id_users = $_POST['id_users'];

$tryb = $_POST['tryb'];



function check($pbase,$pset){



if($pbase>$pset)

	{

		if($pbase-$pset<=10)

		{return true;}

		else

		{return false;}

	}

else

{

	if($pset-$pbase<=10)

	{return true;}

	else

	{return false;}

}

}

function alert()

{

	header('location:graph_user.php?alert='."wrong");

}

function ok()

{

	//echo 'trafiles';

	//header('location:graph_user.php?alert='."wrong");

	$_SESSION['logged']=true;

	header("Location: loggedin.php");

}



try{

	$pdo = new PDO('mysql:host='.config_dbhost.';dbname='.config_dbname, config_dbusername, config_dbpassword);

	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



	



	switch($tryb)

	{

		case 1:

		$p1x=$_POST['p1x'];

		$p1y=$_POST['p1y'];

				

		$stmt = $pdo-> query('select * from graph_point where id_graph_point="'.$id_graph_point.'"');

		//$result = $stmt -> fetchColumn();

		

		foreach ($stmt as $row)

		{

			$p1basex=$row['p1x'];

			$p1basey=$row['p1y'];

			

			if (check($p1basex,$p1x)==true)

			{

				if(check($p1basey,$p1y)==true)

				{

					ok();

				}

				else{alert();}

			}

			else{alert();}

		}



		$stmt -> closeCursor();

		break;

		;

		

		case 2:

		$p1x=$_POST['p1x'];

		$p1y=$_POST['p1y'];

		$p2x=$_POST['p2x'];

		$p2y=$_POST['p2y'];

		

		$stmt = $pdo-> query('select * from graph_point where id_graph_point="'.$id_graph_point.'"');

		//$result = $stmt -> fetchColumn();

		

		foreach ($stmt as $row)

		{

			$p1basex=$row['p1x'];

			$p1basey=$row['p1y'];

			$p2basex=$row['p2x'];

			$p2basey=$row['p2y'];

			if (check($p1basex,$p1x)==true)

			{if(check($p1basey,$p1y)==true)

				{

					if (check($p2basex,$p2x)==true)

					{if(check($p2basey,$p2y)==true)

						{

							ok();

						}else{alert();}

					}else{alert();}

				}else{alert();}

			}else{alert();}

		}



		$stmt -> closeCursor();

		break;



		case 3:

		$p1x=$_POST['p1x'];

		$p1y=$_POST['p1y'];

		$p2x=$_POST['p2x'];

		$p2y=$_POST['p2y'];

		$p3x=$_POST['p3x'];

		$p3y=$_POST['p3y'];

		

		$stmt = $pdo-> query('select * from graph_point where id_graph_point="'.$id_graph_point.'"');

		//$result = $stmt -> fetchColumn();

		

		foreach ($stmt as $row)

		{

			$p1basex=$row['p1x'];

			$p1basey=$row['p1y'];

			$p2basex=$row['p2x'];

			$p2basey=$row['p2y'];

			$p3basex=$row['p3x'];

			$p3basey=$row['p3y'];



			if (check($p1basex,$p1x)==true)

			{if(check($p1basey,$p1y)==true)

				{

					if (check($p2basex,$p2x)==true)

					{if(check($p2basey,$p2y)==true)

						{

							if (check($p3basex,$p3x)==true)

							{if(check($p3basey,$p3y)==true)

								{

									ok();

								}else{alert();}

							}else{alert();}

						}else{alert();}

					}else{alert();}

				}else{alert();}

			}else{alert();}

		}



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

		

		$stmt = $pdo-> query('select * from graph_point where id_graph_point="'.$id_graph_point.'"');

		//$result = $stmt -> fetchColumn();

		

		foreach ($stmt as $row)

		{

			$p1basex=$row['p1x'];

			$p1basey=$row['p1y'];

			$p2basex=$row['p2x'];

			$p2basey=$row['p2y'];

			$p3basex=$row['p3x'];

			$p3basey=$row['p3y'];

			$p4basex=$row['p4x'];

			$p4basey=$row['p4y'];

			if (check($p1basex,$p1x)==true)

			{if(check($p1basey,$p1y)==true)

				{

					if (check($p2basex,$p2x)==true)

					{if(check($p2basey,$p2y)==true)

						{

							if (check($p3basex,$p3x)==true)

							{if(check($p3basey,$p3y)==true)

								{

									if (check($p4basex,$p4x)==true)

									{if(check($p4basey,$p4y)==true)

										{

											ok();

										}else{alert();}

									}else{alert();}

								}else{alert();}

							}else{alert();}

						}else{alert();}

					}else{alert();}

				}else{alert();}

			}else{alert();}

		}



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

		

		$stmt = $pdo-> query('select * from graph_point where id_graph_point="'.$id_graph_point.'"');

		//$result = $stmt -> fetchColumn();

		

		foreach ($stmt as $row)

		{

			$p1basex=$row['p1x'];

			$p1basey=$row['p1y'];

			$p2basex=$row['p2x'];

			$p2basey=$row['p2y'];

			$p3basex=$row['p3x'];

			$p3basey=$row['p3y'];

			$p4basex=$row['p4x'];

			$p4basey=$row['p4y'];

			$p5basex=$row['p5x'];

			$p5basey=$row['p5y'];

			

			

			if (check($p1basex,$p1x)==true)

			{if(check($p1basey,$p1y)==true)

				{

					if (check($p2basex,$p2x)==true)

					{if(check($p2basey,$p2y)==true)

						{

							if (check($p3basex,$p3x)==true)

							{if(check($p3basey,$p3y)==true)

								{

									if (check($p4basex,$p4x)==true)

									{if(check($p4basey,$p4y)==true)

										{

											if (check($p5basex,$p5x)==true)

											{if(check($p5basey,$p5y)==true)

												{

													ok();

												}else{alert();}

											}else{alert();}

										}else{alert();}

									}else{alert();}

								}else{alert();}

							}else{alert();}

						}else{alert();}

					}else{alert();}

				}else{alert();}

			}else{alert();}

		}



		$stmt -> closeCursor();

		break;

	}

	} 

	catch(PDOException $e) {

	echo 'ERROR: ' . $e->getMessage();

	}

?>