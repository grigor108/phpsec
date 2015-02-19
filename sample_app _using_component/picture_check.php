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
//sprawdzenie punktów podczas logowania

if(!isset($_SESSION)) 

    { 

        session_start(); 

    } 



	

$login= $_SESSION['login'];

$id_user_graph = $_POST['id_user_graph'];

$id_users = $_POST['id_users'];

$tryb = $_POST['tryb'];

$id_graph_point = $_POST['id_graph_point'];

$image_adress = $_POST['adres'];





list($width,$height)= getimagesize($image_adress);



echo'

<!DOCTYPE HTML>

<html>

  <head>

    <title>Logowanie obrazkowe</title>

    <meta charset="utf-8" />



	<style type="text/css" media="screen">

    canvas, canvas_check { display:block; margin:1em auto; border:1px solid black; }

    

	canvas { background:url('.$image_adress.') }

  </style>

  	<script type="text/javascript">	



	var id_user_graph = '.$id_user_graph.';

	var id_users = '.$id_users.';

	var id_graph_point = '.$id_graph_point.';

	var tryb ='.$tryb.';

	var login ="'.$login.'";

	

	var xmlhttp;

	var cozmienic;

	var sendtext;

		

	var count=0;

	var img_width='.$width.';

	var img_height='.$height.';

	tabX = new Array('.$tryb.');

	tabY = new Array('.$tryb.');

	var image_adress="'.$image_adress.'";

	



	function podmiendane()

	{

		if (xmlhttp.readyState == 4) {

		if (xmlhttp.status == 200 || xmlhttp.status == 304) {

            cozmienic.innerHTML = xmlhttp.responseText;

		}

		}

	}

	function wyslaniedanych(sendtext)

	{

		if (window.XMLHttpRequest)

		{	

		 xmlhttp = new XMLHttpRequest();

		 cozmienic = document.body;

		 xmlhttp.open("POST","picture_check_with_base.php",true);

		 xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

		 xmlhttp.onreadystatechange = podmiendane;

		 xmlhttp.send(sendtext);



		 

		//xmlhttp.send();

		}

	}



	document.addEventListener("DOMContentLoaded", klikniecie, false);

	

	function klikniecie()

	{

		var canvas = document.getElementById("canvas_check");

		canvas.addEventListener("mousedown", wymiary, false);

		

	}

	function wymiary(event)

	{

	var canvas = document.getElementById("canvas_check");

	var x_obr= new Number();

	var y_obr= new Number();

	var zazn= canvas.getContext("2d");



	

		x_obr = event.clientX + document.body.scrollLeft +

			document.documentElement.scrollLeft;

		y_obr = event.clientY + document.body.scrollTop +

            document.documentElement.scrollTop;

			

        x_obr -= canvas.offsetLeft;

        y_obr -= canvas.offsetTop;

		

		x_obr = Math.round(x_obr);

		y_obr = Math.round(y_obr);



		tabX[count] = x_obr;

		tabY[count] = y_obr;

		

		zazn.beginPath();

		zazn.moveTo(x_obr-10,y_obr-10);

		zazn.lineTo(x_obr+10,y_obr+10);

		zazn.lineWidth = 2;

		zazn.strokeStyle = "red";

		zazn.stroke();

		

		zazn.beginPath();

		zazn.moveTo(x_obr-10,y_obr+10);

		zazn.lineTo(x_obr+10,y_obr-10);

		zazn.lineWidth = 2;

		zazn.strokeStyle = "red";

		zazn.stroke();



		/*

		zazn.lineWidth=3;

		zazn.strokeStyle="yellow";

		zazn.strokeRect(x_obr-10,y_obr-10,20,20);

		*/

		count++;

		

		if (count == tryb)	{





			if(confirm("Potwierdzasz wybór?"))

			{

				switch(tryb){

				

				case 1:

				

				sendtext = "id_user_graph="+id_user_graph +"$login="+login  + "&id_graph_point="+id_graph_point+ "&id_users="+id_users + "&tryb="+tryb+ "&p1x="+tabX[0]+ "&p1y="+tabY[0];

				wyslaniedanych(sendtext);

				break;

				

				case 2:

				sendtext = "id_user_graph="+id_user_graph +"$login="+login + "&id_graph_point="+id_graph_point+ "&id_users="+id_users + "&tryb="+tryb+ "&p1x="+tabX[0]+ "&p1y="+tabY[0]+"&p2x="+tabX[1]+"&p2y="+tabY[1];

				wyslaniedanych(sendtext);

				break;



				case 3:

				sendtext = "id_user_graph="+id_user_graph +"$login="+login + "&id_graph_point="+id_graph_point+ "&id_users="+id_users + "&tryb="+tryb+ "&p1x="+tabX[0]+ "&p1y="+tabY[0]+"&p2x="+tabX[1]+"&p2y="+tabY[1]+"&p3x="+tabX[2]+"&p3y="+tabY[2];

				wyslaniedanych(sendtext);

				break;

				

				case 4:

				sendtext = "id_user_graph="+id_user_graph +"$login="+login + "&id_graph_point="+id_graph_point+ "&id_users="+id_users + "&tryb="+tryb+ "&p1x="+tabX[0]+ "&p1y="+tabY[0]+"&p2x="+tabX[1]+"&p2y="+tabY[1]+"&p3x="+tabX[2]+"&p3y="+tabY[2]+"&p4x="+tabX[3]+"&p4y="+tabY[3];

				wyslaniedanych(sendtext);

				break;

				

				case 5:

				sendtext = "id_user_graph="+id_user_graph +"$login="+login + "&id_graph_point="+id_graph_point+ "&id_users="+id_users + "&tryb="+tryb+ "&p1x="+tabX[0]+ "&p1y="+tabY[0]+"&p2x="+tabX[1]+"&p2y="+tabY[1]+"&p3x="+tabX[2]+"&p3y="+tabY[2]+"&p4x="+tabX[3]+"&p4y="+tabY[3]+"&p5x="+tabX[4]+"&p5y="+tabY[4];

				wyslaniedanych(sendtext);

				break;

				}

				

			}

			else{

					alert("Wybierz jeszcze raz");

					

					count=0;

					zazn.clearRect(0,0,img_width,img_height);

			}

			



		}

	}

	</script>

 </head>



  <body>

    <canvas id="canvas_check" width='.$width.'; height='.$height.';>

	</canvas>

  </body>

</html> '

?>