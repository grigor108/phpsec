<?php
/*
Plugin Name: PHP Security 
Description: Four-Factor Authentication for WordPress.
Author: Pawel Wlizlo, Grzegorz Olszewski, Judyta Hoffman
Version: 1.0
Compatibility: WordPress 4.1
*/
 
 /*Copyright 2014 Judyta Hofman, Grzegorz Olszewski, Paweł Wlizło

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
//require("config.php");
//require("functions.php");

date_default_timezone_set(config_default_timezone);


/**
CONFIG FUNCTIONS STARTS HERE
**/

//database config below
global $wpdb;
global $PHPSEC_db_version;

$prefix = $wpdb->prefix;
define ('config_dbprefix', $prefix, true);
$GLOBALS['safe_point_check']=0000;

//require_once('functions.php'); //required to properly execute settings-reading function below
 
//global settings
define ('config_error_reporting_level', read_settings('config_error_reporting_level'), true);
define ('config_default_timezone', read_settings('config_default_timezone'), true);

//mailer config below
define ('config_mail_from', read_settings('config_mail_from'), true);
define ('config_mail_from_name', read_settings('config_mail_from_name'), true);
define ('config_mail_reply_to', read_settings('config_mail_reply_to'), true);
define ('config_mail_reply_to_name', read_settings('config_mail_reply_to_name'), true);
define ('config_mail_subject', read_settings('config_mail_subject'), true);

//sms config below
define ('config_sms_api', read_settings('config_sms_api'), true);
/**
CONFIG FUNCTIONS STOPS HERE
**/

    add_action( 'init', 'init');
    
function init() {
   
    //require("libs/GoogleAuthenticator/GoogleAuthenticator.php");
    //require(ABSPATH . "/wp-content/plugins/phpsec/libs/googleauthenticator/googleauthenticator.php"); 
    $dir = plugin_dir_path( __FILE__ );
    require_once($dir. "libs/phpmailer/phpmailerautoload.php");
    require_once($dir. "libs/GoogleAuthenticator/GoogleAuthenticator.php"); 
    
    if(!function_exists('wp_get_current_user')) {
        include(ABSPATH . "wp-includes/pluggable.php");
    }
    add_action( 'admin_menu', 'my_plugin_menu' ); /// add new menu
    add_filter('wp_authenticate_user', 'auth_step1',10,2);
    
}


/**
 * Logged user informations
 **/
/*global $current_user;
$login = $current_user->user_login;
$id_login = $current_user->ID;
*/

/**
CREATE TABLES NEEDED FOR PHPSEC
**/

register_activation_hook(__FILE__,'when_plugin_install');
function when_plugin_install() {
global $wpdb;
$PHPSEC_db_version = "1.0";
$tb_name = config_dbprefix . "graph_point";
$charset_collate = $wpdb->get_charset_collate();
if($wpdb->get_var("show tables like '$tb_name'") != $tb_name){
	
	$sql = "CREATE TABLE ".$tb_name." (
    id_graph_point int(11) NOT NULL AUTO_INCREMENT,
    p1x varchar(256) DEFAULT NULL,
    p2x varchar(256) DEFAULT NULL,
    p3x varchar(256) DEFAULT NULL,
    p4x varchar(256) DEFAULT NULL,
    p5x varchar(256) DEFAULT NULL,
    p1y varchar(256) DEFAULT NULL,
    p2y varchar(256) DEFAULT NULL,
    p3y varchar(256) DEFAULT NULL,
    p4y varchar(256) DEFAULT NULL,
    p5y varchar(256) DEFAULT NULL,
    PRIMARY KEY (id_graph_point)
	) ".$charset_collate.";";
    
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

$tb_name = config_dbprefix ."phpsec_settings";
if($wpdb->get_var("show tables like '$tb_name'") != $tb_name){
		
	$sql="
	CREATE TABLE IF NOT EXISTS `".$tb_name."` (
  `config_error_reporting_level` text NOT NULL,
  `config_default_timezone` text NOT NULL,
  `config_mail_from` text NOT NULL,
  `config_mail_from_name` text NOT NULL,
  `config_mail_reply_to` text NOT NULL,
  `config_mail_reply_to_name` text NOT NULL,
  `config_mail_subject` text NOT NULL,
  `config_sms_api` text NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
  ) ;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

$tb_name = config_dbprefix ."user_graph";
if($wpdb->get_var("show tables like '$tb_name'") != $tb_name){
		
	$sql="
	CREATE TABLE IF NOT EXISTS `".$tb_name."` (
	  `id_user_graph` int(11) NOT NULL AUTO_INCREMENT,
	  `id_users` int(11) NOT NULL,
	  `picture_number` int(11) NOT NULL,
      `picture_name` varchar(256) NOT NULL,
	  `mode` int(11) NOT NULL,
	  `id_graph_point` int(11) NOT NULL,	  
	  PRIMARY KEY(`id_user_graph`),
	  FOREIGN KEY (`id_users`) REFERENCES `".config_dbprefix."users` (`id`),
	  FOREIGN KEY (`id_graph_point`) REFERENCES `".config_dbprefix."graph_point` (`id_graph_point`)
	) ;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

$tb_name = config_dbprefix ."user_settings";
if($wpdb->get_var("show tables like '$tb_name'") != $tb_name){
    
	$sql="
	CREATE TABLE IF NOT EXISTS `".$tb_name."` (
	  `id_user_settins` int(11) NOT NULL AUTO_INCREMENT,
      `id_users` int(11) NOT NULL,
      `email` text DEFAULT NULL,
      `auth_mechanism` varchar(64) DEFAULT NULL,
      `current_code` varchar(64) DEFAULT NULL,
      `image_hitboxes` blob DEFAULT NULL,
      `path_to_image_password` text DEFAULT NULL,
      `phone` varchar(64) DEFAULT NULL,
      `gauth_secret` text DEFAULT NULL,
      PRIMARY KEY (`id_user_settins`),
	  FOREIGN KEY (`id_users`) REFERENCES `".config_dbprefix."users` (`id`)
	) ;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

/**
 Extend personal profile page with PHP Security settings.
 **/
}
function my_plugin_menu() {		/// new menu values
	add_options_page( 'PHPSEC settings', 'PHPSEC', 'manage_options', 'phpsec_settings', 'profile_personal_options' );
    add_options_page( 'PHPSEC image settings', 'PHPSEC image', 'manage_options', 'phpsec_settings_image', 'profile_personal_options_image' );
}

function profile_personal_options_image() 
{
    $dir_for_RII = plugin_dir_path (__FILE__ );
    $dir_for_thumb= plugin_dir_url( __FILE__ ).'img/';
    
    global $user_id, $is_profile_page;
    global $current_user;
    
    $login = $current_user->user_login;
    $id = $current_user->ID;
    $auth_mech=get_user_settings($id,'auth_mechanism');
    
	if ( $is_profile_page || IS_PROFILE_PAGE ) 
    { 
        /// PHPSEC IMAGE header, css style put here
        global $wpdb;
        echo'   <style>
	            .thumbnail {
                float: left;
                width: 200px;
                height: 170px;
                margin: 15px;
	            }</style>
                <div class="wrap">
                <h2>PHPSEC Settings</h2><br>
                <h2>Image authentication settings</h2>
               ';
        
        /// action for save selected image and points into database
        if($_GET['action']=='save')
        {
            $mode = esc_attr($_POST['tryb']);
            $picture_name = esc_attr($_POST['picture']);
            $wich_picture_count = $wpdb->get_var( 
            "SELECT picture_number FROM 
            ".config_dbprefix."user_graph WHERE 
            id_users = ".$id." order by picture_number desc LIMIT 1" ); // if user has more pictures, this is their numeration
            if($wich_picture_count==null)
            {
                $wich_picture_count=0;
            }
            picture_set_to_base($id,$mode,$picture_name,$wich_picture_count);
        }
        /// action for removing image from user configuration
        if($_GET['action']=='remove')
        {
            $picture_number = esc_attr($_POST['picture_number']);
            picture_remove_from_base($id,$picture_number);
        }
        /// DEFAULT PAGE PHPSEC IMAGE
        if($_GET['action_user']==''||!isset($_GET['action_user']))
        {
            if(get_user_graph_settings($id)=='') // if user hasn't any images
            {
                echo ' 
                User has not set any image for authentication.
                <form method="POST" action="options-general.php?page=phpsec_settings_image&action_user=add_image">
		        Do you want to set it now?<p>
		        <input id="submit" class="button button-primary" type="submit" value="Add image authentication">
                </div>';
            }
            else
            {/// if user has at least one image 
                global $wpdb;
                $stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'user_graph WHERE id_users ='.$id.' order by picture_number');
                $result=array();
                
                foreach ($stmt as $stmts ) 
                {array_push($result, (array)$stmts);}
                echo ' 
                User has this image:<p>';
                foreach($result as $row)
                {
                echo'
				<div class =thumbnail>
                <form method="POST" action="options-general.php?page=phpsec_settings_image&action=remove">
					<img src="'.$dir_for_thumb.$row["picture_name"].'" width="190" height=160"><br>
					    No. '.$row["picture_number"].' 
                    <input type="hidden" name="picture_number" value="'.$row["picture_number"].'" />
                <input id="submit" class="button button-primary" type="submit" value="Remove">
                </form>
				</div>';
                }
                echo '<p>
                <form method="POST" action="options-general.php?page=phpsec_settings_image&action_user=add_image">
		        Do you want to set another image?<p>
		        <input id="submit" class="button button-primary" type="submit" value="Add image authentication">
                </form>
                </div>';
            }
        }
        #region
        ///action for setting points on selected image
        if ($_GET['action_user']=='set_points')
        {
            $image_name = esc_attr($_POST['adres']);
            $image_adress =$dir_for_thumb.$image_name;
            $mode = esc_attr($_POST['mode']);
            
            list($width,$height)= getimagesize($image_adress);
            echo'
            <style type="text/css" media="screen">
            canvas, canvas_check { display:block; margin:1em auto; border:1px solid black; }
	        canvas { background:url('.$image_adress.') }
            </style>

              	<script type="text/javascript">	
	        var login ="'.$login.'";
	        var tryb ='.$mode.';
	        var xmlhttp;
	        var cozmienic;
	        var sendtext;

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
		         xmlhttp.open("POST","options-general.php?page=phpsec_settings_image&action=save",true);
		         xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		         xmlhttp.onreadystatechange = podmiendane;
		         xmlhttp.send(sendtext);

		 
		        //xmlhttp.send();
		        }
	        }

	        document.addEventListener("DOMContentLoaded", klikniecie, false);
	
	        var count=0;
	        var img_width='.$width.';
	        var img_height='.$height.';
	        tabX = new Array('.$mode.');
	        tabY = new Array('.$mode.');
	        var image_adress="'.$image_adress.'";
            var image_name = "'.$image_name.'";
	
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
			
                    if(document.getElementById("adminmenuback").offsetWidth!=0)
                    {
                        var x_ofset= document.getElementById("adminmenuback").offsetWidth+20;
                    }
                    else
                    {
                       var x_ofset= document.getElementById("adminmenuback").offsetWidth+10;
                    }
                x_obr -= canvas.offsetLeft + x_ofset;
                y_obr -= canvas.offsetTop + document.getElementById("wpadminbar").clientHeight;
		
		        x_obr = Math.round(x_obr);
		        y_obr = Math.round(y_obr);

		        tabX[count] = x_obr;
		        tabY[count] = y_obr;
		        zazn.lineWidth=3;
		        zazn.strokeStyle="yellow";
		        zazn.strokeRect(x_obr-10,y_obr-10,20,20);
		        zazn.lineWidth=1;
		        zazn.font = "15px Arial";
		        zazn.strokeText(count,x_obr+15,y_obr+15);
		        count++;
		
		        if (count == tryb)	{


			        if(confirm("Confirm?"))
			        {
				        switch(tryb){
				
				        case 1:
				
				        sendtext = "login="+login + "&tryb="+tryb+ "&picture="+image_name + "&p1x="+tabX[0]+ "&p1y="+tabY[0];
				        wyslaniedanych(sendtext);
				        break;
				
				        case 2:
				        sendtext = "login="+login + "&tryb="+tryb+ "&picture="+image_name + "&p1x="+tabX[0]+ "&p1y="+tabY[0]+"&p2x="+tabX[1]+"&p2y="+tabY[1];
				        wyslaniedanych(sendtext);
				        break;

				        case 3:
				        sendtext = "login="+login + "&tryb="+tryb+ "&picture="+image_name + "&p1x="+tabX[0]+ "&p1y="+tabY[0]+"&p2x="+tabX[1]+"&p2y="+tabY[1]+"&p3x="+tabX[2]+"&p3y="+tabY[2];
				        wyslaniedanych(sendtext);
				        break;
				
				        case 4:
				        sendtext = "login="+login + "&tryb="+tryb+ "&picture="+image_name + "&p1x="+tabX[0]+ "&p1y="+tabY[0]+"&p2x="+tabX[1]+"&p2y="+tabY[1]+"&p3x="+tabX[2]+"&p3y="+tabY[2]+"&p4x="+tabX[3]+"&p4y="+tabY[3];
				        wyslaniedanych(sendtext);
				        break;
				
				        case 5:
				        sendtext = "login="+login + "&tryb="+tryb+ "&picture="+image_name + "&p1x="+tabX[0]+ "&p1y="+tabY[0]+"&p2x="+tabX[1]+"&p2y="+tabY[1]+"&p3x="+tabX[2]+"&p3y="+tabY[2]+"&p4x="+tabX[3]+"&p4y="+tabY[3]+"&p5x="+tabX[4]+"&p5y="+tabY[4];
				        wyslaniedanych(sendtext);
				        break;
				        }
				
			        }
			        else{
					        alert("Choose again");
					
					        count=0;
					        zazn.clearRect(0,0,img_width,img_height);
			        }
		                }
	                }
	        </script>
            <canvas id="canvas_check" width='.$width.'; height='.$height.';>
	        </canvas></div>
            ';
            
        }
        ///action for add new image to user configuration, after that is action set_points
        if ($_GET['action_user']=='add_image')
        {
            echo'

                <p>
                <h3> Choose image</h3>
                Choose picture which will be used as authenticate image<br>';
            
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir_for_RII .'img',RecursiveDirectoryIterator::SKIP_DOTS| FilesystemIterator::UNIX_PATHS)) as $filename)
            {
                $cut_filename = substr($filename, strrpos($filename, '/') + 1);
                $picture = $dir_for_thumb.$cut_filename;
                echo '	
		  	        <form method="POST" action="options-general.php?page=phpsec_settings_image&action_user=set_points">
			        <div class =thumbnail>
				        <img src="'.$picture.'" width="190" height=160">
				        Number of points:
					        <select name="mode">
					        <option value="1">1</option>
					        <option value="2">2</option>
					        <option value="3">3</option>
					        <option value="4">4</option>
					        <option value="5">5</option>
					        </select>
				        <input type="hidden" name="adres" value="'.$cut_filename.'" />
				        <input id="submit" class="button button-primary" type="submit" value="Choose">
				        </div>
				        </form>
                    </div>';
            }
        }
    }
}

function profile_personal_options() 
    {
	global $user_id, $is_profile_page;
    global $current_user;
    $login = $current_user->user_login;
    $id = $current_user->ID;
    
	if ( $is_profile_page || IS_PROFILE_PAGE ) {
		if ($_GET['action_global']=='change')
		{
			set_settings('config_error_reporting_level',addslashes($_POST['config_error_reporting_level']));
			set_settings('config_default_timezone',addslashes($_POST['config_default_timezone']));
			set_settings('config_mail_from',addslashes($_POST['config_mail_from']));
			set_settings('config_mail_from_name',addslashes($_POST['config_mail_from_name']));
			set_settings('config_mail_reply_to',addslashes($_POST['config_mail_reply_to']));
			set_settings('config_mail_reply_to_name',addslashes($_POST['config_mail_reply_to_name']));
			set_settings('config_mail_subject',addslashes($_POST['config_mail_subject']));
			set_settings('config_sms_api',addslashes($_POST['config_sms_api']));
		}
        //save user settings 
        if ($_GET['action_user']=='change')
        {
            
            if ($_POST['auth_mechanism']=='image')
                {
                    if(get_user_graph_settings($id)=='')
                    {
                        echo'
                        <script type="text/javascript">	
                        alert("First set at least one image!");
                        </script>
                        ';
                    }
                    else
                    {
                        set_user_settings('auth_mechanism',$_POST['auth_mechanism']);
                        set_user_settings('phone',$_POST['phone']);
                    }
    
                }
            else{
            set_user_settings('auth_mechanism',$_POST['auth_mechanism']);
            set_user_settings('phone',$_POST['phone']);}
            
        }
        if ($_GET['action_user']=='new_secret')
        {
            $time = floor(time() / 30);
            $gq = new GoogleAuthenticator();
            $secret=$gq->generateSecret();

            echo '<div class="wrap">';
            echo '<a href="';
            echo "https://www.google.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth://totp/PHPSEC:".$login."@".$_SERVER['HTTP_HOST']."?secret=".$secret;
            echo '" target="_blank" rel = "noreferrer">GOOGLE AUTHENTICATOR KEY QR CODE LINK</a>';
            echo '</div>';
            set_user_settings('gauth_secret',$secret);
        }
        global $wpdb;
        $auth_mech=get_user_settings($id,'auth_mechanism');
        echo '
			<div class="wrap">
			<h2>PHPSEC Settings</h2>
			<form method="POST" action="options-general.php?page=phpsec_settings&action_global=change">
			<h2> Global settings </h2><br>
			PHP error reporting level (e.g. E_ALL~E_NOTICE): <input type="text" name="config_error_reporting_level" value="'.stripslashes(read_settings('config_error_reporting_level')).'"><br>
			Default timezone in PHP format (e.g. Europe/Berlin): <input type="text" name="config_default_timezone" value="'.stripslashes(read_settings('config_default_timezone')).'" size="30"><br>
			<h3> Mail settings </h3><br>
				E-mail auth sender address (e.g. something@example.com): <input type="mail" name="config_mail_from" value="'.stripslashes(read_settings('config_mail_from')).'" size="45"><br>
				E-mail auth sender name: <input type="text" name="config_mail_from_name" value="'.stripslashes(read_settings('config_mail_from_name')).'" size="35"><br>
				E-mail auth reply-to address (e.g. reply@example.com): <input type="mail" name="config_mail_reply_to" value="'.stripslashes(read_settings('config_mail_reply_to')).'" size="45"><br>
				E-mail auth reply-to name:: <input type="text" name="config_mail_reply_to_name" value="'.stripslashes(read_settings('config_mail_reply_to_name')).'" size="35"><br>
				E-mail auth subject: <input type="text" name="config_mail_subject" value="'.stripslashes(read_settings('config_mail_subject')).'" size="85"><br>
			<h3> SMS settings </h3><br>
			SMS API URL: <input type="text" name="config_sms_api" value="'.stripslashes(read_settings('config_sms_api')).'" size="100"><br>
				<br>
                <p class ="submit">
			<input id="submit" class="button button-primary" type="submit" value="SAVE">
            </p>
			</form>

            <form method="POST" action="options-general.php?page=phpsec_settings&action_user=change">
            <h2> User settings </h2><br>
            <h3> Authentication method </h3><br>
            
             <select name="auth_mechanism">
             
                 <option value="" ';
                if ($auth_mech=="") echo "selected";
                echo '>No additional authentication</option>
                <option value="email" ';
                if ($auth_mech=="email") echo "selected";
                echo '>Email</option>
	            <option value="sms"';
	            if ($auth_mech=="sms") echo "selected";
	            echo '>SMS</option>
	            <option value="image"';
	            if ($auth_mech=="image") echo "selected";
	            echo '>Image password</option>
	            <option value="gauth"';
	            if ($auth_mech=="gauth") echo "selected";
	            echo '>Google authenticator</option>
	            </select> 
            <h3> Additional info </h3><br>
	            Phone (with country code (e.g. +44736525532): <input type="text" name="phone" value="'.get_user_settings($id,'phone').'" size="45"><br>
	            <a href="options-general.php?page=phpsec_settings&action_user=new_secret" target="_blank"> Generate new Google Authenticator Secret to pair with your app</a>

	            <br>
            <p class ="submit">
			<input id="submit" class="button button-primary" type="submit" value="SAVE">
            </p>
            </form>
			</div>
			';

	}
}

/// EVERYTHING FROM FUNCTIONS.php IS HERE///////////////////////////////////////////////////////////////////////////////////////////

/// FUNCTIONS THAT OPERATES ON DATABASE//////////////////////////////////////////////////////////////////////////////////////////////

function get_user_data()
{
	try 
	{
        global $current_user;
        $login = $current_user->user_login;
        $id_login = $current_user->ID;
        global $wpdb;
		$stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'user_settings WHERE id_users ='.$id_login.'');
		$result=array();
        
        foreach ($stmt as $stmts ) 
        {array_push($result, (array)$stmts);}
		$result_final=$result[0];
	}
	catch(PDOException $e) 
	{
        echo 'Error: ' . $e->getMessage();
	}
	return $result_final;
}
function get_user_settings($id,$settings)
{
	try 
	{
        global $wpdb;
		$stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'user_settings WHERE id_users ='.$id.'');
		$result=array();
        
        foreach ($stmt as $stmts ) 
        {array_push($result, (array)$stmts);}
		$result_final=$result[0][$settings];
	}
	catch(PDOException $e) 
	{
        echo 'Error: ' . $e->getMessage();
	}
	return $result_final;
}
/**
Read_settings function - read to options file/settings
**/
function read_settings($setting) //reading passed setting value from db (must be exactly the same as column name in db)
{ 
	try 
	{
		global $wpdb;
		$stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'phpsec_settings');
		$result=array();
		
		foreach ($stmt as $stmts ) 
        {array_push($result, (array)$stmts);}
		$setting_value=$result[0][$setting];
	} 
	catch(PDOException $e)
	{echo 'ERROR: ' . $e->getMessage();}
	return $setting_value;
}

function get_user_graph_settings($id) //reading passed setting value from db (must be exactly the same as column name in db)
{ 
	try 
	{
		global $wpdb;
		$stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'user_graph WHERE id_users ='.$id.'');
		$result=array();
		
		foreach ($stmt as $stmts ) 
        {array_push($result, (array)$stmts);}
		$setting_value=$result[0];
	} 
	catch(PDOException $e)
	{echo 'ERROR: ' . $e->getMessage();}
	return $setting_value;
}

/**
Save_settings function
 **/
function save_settings($setting) //reading passed setting value from db (must be exactly the same as column name in db)
{
	try 
	{
        global $wpdb;
		$stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'phpsec_settings');
		$result=array();
		
		foreach ($stmt as $stmts ) {
            array_push($result, (array)$stmts);
        }
		$setting_value=$result[0][$setting];
	} 
	catch(PDOException $e) 
	{echo 'ERROR: ' . $e->getMessage();}
	return $setting_value;

}

/**
Set_settings function - options/settings global
 **/
function set_settings($setting_name,$setting_value)
{
	try 
	{
        global $wpdb;
        
        $stmt = $wpdb->get_results('SELECT id FROM '.config_dbprefix.'phpsec_settings');
		$result=array();
		
		foreach ($stmt as $stmts ) {
            array_push($result, (array)$stmts);
        }
		$id_setting_value=$result[0]['id'];
		
        $wpdb->update( 
	    config_dbprefix.'phpsec_settings', 
        array($setting_name=>$setting_value), 
        array('id' => $id_setting_value) );
	} 
	catch(PDOException $e)
	{echo 'Error: ' . $e->getMessage();}
	return true;
}

function set_user_settings($setting_name,$setting_value)
{
	try 
	{
        global $current_user;
        $login = $current_user->user_login;
        $id_login = $current_user->ID;
        global $wpdb;
        
        $check_if_exist = $wpdb->get_var( 
            "SELECT COUNT(*) FROM 
            ".config_dbprefix."user_settings WHERE 
            id_users = ".$id_login."" );
        //echo $check_if_exist;
        
        if($check_if_exist!=0)
        {
            $wpdb->update( 
            config_dbprefix.'user_settings', 
            array($setting_name=>$setting_value), 
            array('id_users' => $id_login) );

        }
        else
        {
            $wpdb->insert( 
            config_dbprefix.'user_settings', 
            array('id_users'=>$id_login, $setting_name=>$setting_value));
        }
	} 
	catch(PDOException $e) 
	{echo 'Error: ' . $e->getMessage();}
	return true;
}

function picture_set_to_base($id,$mode,$picture_name,$wich_picture_count)
{
    global $wpdb;
    
    switch($mode)
    {
        case 1:

            $p1x=esc_attr($_POST['p1x']);
            $p1y=esc_attr($_POST['p1y']);
            $wich_picture_count = $wich_picture_count + 1;
            
            $wpdb->insert( 
            config_dbprefix.'graph_point', 
            array('p1x'=>$p1x, 'p1y'=>$p1y));
            $insertid = $wpdb->insert_id;
            $wpdb->insert( 
            config_dbprefix.'user_graph', 
            array('id_users'=>$id, 'picture_number'=>$wich_picture_count,'picture_name'=>$picture_name,'mode'=>$mode,'id_graph_point'=>$insertid));
            //wp_redirect( 'options-general.php?page=phpsec_settings_image', 301 );
            wp_redirect( get_permalink( $post->post_parent ));
            break;
            
        case 2:

            $p1x=esc_attr($_POST['p1x']);
            $p1y=esc_attr($_POST['p1y']);
            $p2x=esc_attr($_POST['p2x']);
            $p2y=esc_attr($_POST['p2y']);
            $wich_picture_count = $wich_picture_count + 1;
            
            $wpdb->insert( 
            config_dbprefix.'graph_point', 
            array('p1x'=>$p1x, 'p1y'=>$p1y,'p2x'=>$p2x, 'p2y'=>$p2y));
            $insertid = $wpdb->insert_id;
            $wpdb->insert( 
            config_dbprefix.'user_graph', 
            array('id_users'=>$id, 'picture_number'=>$wich_picture_count,'picture_name'=>$picture_name,'mode'=>$mode,'id_graph_point'=>$insertid));
            //wp_redirect( 'options-general.php?page=phpsec_settings_image', 301 );
            wp_redirect( get_permalink( $post->post_parent ));
            break;        
            
        case 3:

            $p1x=esc_attr($_POST['p1x']);
            $p1y=esc_attr($_POST['p1y']);
            $p2x=esc_attr($_POST['p2x']);
            $p2y=esc_attr($_POST['p2y']);
            $p3x=esc_attr($_POST['p3x']);
            $p3y=esc_attr($_POST['p3y']);
            $wich_picture_count = $wich_picture_count + 1;
            
            $wpdb->insert( 
            config_dbprefix.'graph_point', 
            array('p1x'=>$p1x, 'p1y'=>$p1y,'p2x'=>$p2x, 'p2y'=>$p2y,'p3x'=>$p3x, 'p3y'=>$p3y));
            $insertid = $wpdb->insert_id;
            $wpdb->insert( 
            config_dbprefix.'user_graph', 
            array('id_users'=>$id, 'picture_number'=>$wich_picture_count,'picture_name'=>$picture_name,'mode'=>$mode,'id_graph_point'=>$insertid));
            //wp_redirect( 'options-general.php?page=phpsec_settings_image', 301 );
            wp_redirect( get_permalink( $post->post_parent ));
            break;
        
        case 4:

            $p1x=esc_attr($_POST['p1x']);
            $p1y=esc_attr($_POST['p1y']);
            $p2x=esc_attr($_POST['p2x']);
            $p2y=esc_attr($_POST['p2y']);
            $p3x=esc_attr($_POST['p3x']);
            $p3y=esc_attr($_POST['p3y']);
            $p4x=esc_attr($_POST['p4x']);
            $p4y=esc_attr($_POST['p4y']);
            $wich_picture_count = $wich_picture_count + 1;
            
            $wpdb->insert( 
            config_dbprefix.'graph_point', 
            array('p1x'=>$p1x, 'p1y'=>$p1y,'p2x'=>$p2x, 'p2y'=>$p2y,'p3x'=>$p3x, 'p3y'=>$p3y,'p4x'=>$p4x, 'p4y'=>$p4y));
            $insertid = $wpdb->insert_id;
            $wpdb->insert( 
            config_dbprefix.'user_graph', 
            array('id_users'=>$id, 'picture_number'=>$wich_picture_count,'picture_name'=>$picture_name,'mode'=>$mode,'id_graph_point'=>$insertid));
            //wp_redirect( 'options-general.php?page=phpsec_settings_image', 301 );
            wp_redirect( get_permalink( $post->post_parent ));
            break;
        
        case 5:

            $p1x=esc_attr($_POST['p1x']);
            $p1y=esc_attr($_POST['p1y']);
            $p2x=esc_attr($_POST['p2x']);
            $p2y=esc_attr($_POST['p2y']);
            $p3x=esc_attr($_POST['p3x']);
            $p3y=esc_attr($_POST['p3y']);
            $p4x=esc_attr($_POST['p4x']);
            $p4y=esc_attr($_POST['p4y']);
            $p5x=esc_attr($_POST['p5x']);
            $p5y=esc_attr($_POST['p5y']);
            $wich_picture_count = $wich_picture_count + 1;
            
            $wpdb->insert( 
            config_dbprefix.'graph_point', 
            array('p1x'=>$p1x, 'p1y'=>$p1y,'p2x'=>$p2x, 'p2y'=>$p2y,'p3x'=>$p3x, 'p3y'=>$p3y,'p4x'=>$p4x, 'p4y'=>$p4y,'p5x'=>$p5x, 'p5y'=>$p5y));
            $insertid = $wpdb->insert_id;
            $wpdb->insert( 
            config_dbprefix.'user_graph', 
            array('id_users'=>$id, 'picture_number'=>$wich_picture_count,'picture_name'=>$picture_name,'mode'=>$mode,'id_graph_point'=>$insertid));
            //wp_redirect( 'options-general.php?page=phpsec_settings_image', 301 );
            wp_redirect( get_permalink( $post->post_parent ));
            break;
    }
}

function picture_remove_from_base($id,$picture_number)
{
    global $wpdb;
    $id_graph_point = $wpdb->get_var( 
            "SELECT id_graph_point FROM 
            ".config_dbprefix."user_graph WHERE 
            id_users = ".$id." and picture_number =".$picture_number."" );
    
    $wpdb->delete( 
            config_dbprefix.'user_graph', 
            array('id_users'=>$id,'picture_number'=>$picture_number,'id_graph_point'=>$id_graph_point));
    
    $wpdb->delete( 
    config_dbprefix.'graph_point', 
    array('id_graph_point'=>$id_graph_point));
        
    $auth_mech=get_user_settings($id,'auth_mechanism');
    if($auth_mech=='image')
    {
        if(get_user_graph_settings($id)=='')
            {
            echo' <script type="text/javascript">	
                            alert("You remove the last image, authentication method will be set to default (No additional authentication)!");
                            </script>';
            set_user_settings('auth_mechanism','');
            }
    }
    wp_redirect( get_permalink( $post->post_parent ));
    //wp_redirect( 'options-general.php?page=phpsec_settings_image', 301 );
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

 /// Function auth_step_1


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
       // echo 'Message could not be sent.';
		//echo 'Mailer Error: ' . $mail->ErrorInfo;
        return false;
	} 
	else 
	{
		//echo 'Message has been sent';
        return true;
	}
}

function generate_new_code($id)

{
	$code=rand (1111,9999);
   // $pdo->query('SET NAMES utf8');
    
    global $wpdb;
    //global $current_user;
    //$id_login = $current_user->ID;
    
    $wpdb->update( 
    config_dbprefix.'user_settings', 
    array('current_code'=>$code), 
    array('id_users' => $id) );
             
   /* $stmt = $wpdb->prepare('UPDATE '.config_dbprefix.'users SET `current_code`=:code WHERE login = :login AND password = :password');
    
    $stmt->execute(array(':code' => $code,
    ':login' => $login,
    ':password' => $password
    ));*/
    return $code;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function verify_user_data($id,$auth_mechanism,$current_code)
{
    try 
    {
        global $wpdb;
    
		$stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'user_settings WHERE id_users ='.$id.'');
		$result=array();
        
        foreach ($stmt as $stmts ) 
        {array_push($result, (array)$stmts);}
		//$result_final=$result[0];
        
        
        //Google Authenticator check handling
        if ($auth_mechanism=='gauth')
        {
            if(!function_exists('GoogleAuthenticator')) {
                $dir = plugin_dir_path( __FILE__ );
                require_once($dir. "libs/GoogleAuthenticator/GoogleAuthenticator.php"); 
            }
            $time = floor(time() / 30);
            $gaa = new GoogleAuthenticator();
            $secret=$result[0]['gauth_secret'];
            
            if ($gaa->checkCode($secret,$current_code)) $verification=true;
            else $verification=false;
        }
        else{
            if($id==$result[0]['id_users'] AND $auth_mechanism==$result[0]['auth_mechanism'] AND $current_code==$result[0]['current_code']){ $verification = true;}
            else {$verification = false;}
        }
	}
	catch(PDOException $e) 
	{
		echo 'Error: ' . $e->getMessage();
    }
	return $verification;
}

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

function picture_check_with_base($mode,$id_graph_point)
{
    global $wpdb;
    switch($mode)
	{
		case 1:
            $p1x=esc_attr($_POST['p1x']);
            $p1y=esc_attr($_POST['p1y']);
                        
            $stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'graph_point WHERE id_graph_point ='.$id_graph_point.'');
            $result=array();
            
            foreach ($stmt as $stmts ) 
            {array_push($result, (array)$stmts);}
            
            //$result = $stmt -> fetchColumn();
            
                $p1basex=$result[0]['p1x'];
                $p1basey=$result[0]['p1y'];
                
                if (check($p1basex,$p1x)==true)
                {
                    if(check($p1basey,$p1y)==true)
                    {
                        return true;
                    }
                    else{return false;}
                }
                else{return false;}

            break;
            
		
		case 2:
            $p1x=esc_attr($_POST['p1x']);
            $p1y=esc_attr($_POST['p1y']);
            $p2x=esc_attr($_POST['p2x']);
            $p2y=esc_attr($_POST['p2y']);
            
            $stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'graph_point WHERE id_graph_point ='.$id_graph_point.'');
            $result=array();
            
            foreach ($stmt as $stmts ) 
            {array_push($result, (array)$stmts);}
            
            
                $p1basex=$result[0]['p1x'];
                $p1basey=$result[0]['p1y'];
                $p2basex=$result[0]['p2x'];
                $p2basey=$result[0]['p2y'];
                if (check($p1basex,$p1x)==true)
                {
                    if(check($p1basey,$p1y)==true)
                    {
                        if (check($p2basex,$p2x)==true)
                        {
                            if(check($p2basey,$p2y)==true)
                            {
                                return true;
                            }else{return false;}
                        }else{return false;}
                    }else{return false;}
                }else{return false;}
            
            break;

		case 3:
            $p1x=esc_attr($_POST['p1x']);
            $p1y=esc_attr($_POST['p1y']);
            $p2x=esc_attr($_POST['p2x']);
            $p2y=esc_attr($_POST['p2y']);
            $p3x=esc_attr($_POST['p3x']);
            $p3y=esc_attr($_POST['p3y']);
            
            $stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'graph_point WHERE id_graph_point ='.$id_graph_point.'');
            $result=array();
            
            foreach ($stmt as $stmts ) 
            {array_push($result, (array)$stmts);}
            
                $p1basex=$result[0]['p1x'];
                $p1basey=$result[0]['p1y'];
                $p2basex=$result[0]['p2x'];
                $p2basey=$result[0]['p2y'];
                $p3basex=$result[0]['p3x'];
                $p3basey=$result[0]['p3y'];

                if (check($p1basex,$p1x)==true)
                {
                    if(check($p1basey,$p1y)==true)
                    {
                        if (check($p2basex,$p2x)==true)
                        {
                            if(check($p2basey,$p2y)==true)
                            {
                                if (check($p3basex,$p3x)==true)
                                {
                                    if(check($p3basey,$p3y)==true)
                                    {
                                        return true;
                                    }else{return false;}
                                }else{return false;}
                            }else{return false;}
                        }else{return false;}
                    }else{return false;}
                }else{return false;}
            
            break;
		
		case 4:
            $p1x=esc_attr($_POST['p1x']);
            $p1y=esc_attr($_POST['p1y']);
            $p2x=esc_attr($_POST['p2x']);
            $p2y=esc_attr($_POST['p2y']);
            $p3x=esc_attr($_POST['p3x']);
            $p3y=esc_attr($_POST['p3y']);
            $p4x=esc_attr($_POST['p4x']);
            $p4y=esc_attr($_POST['p4y']);
            
            $stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'graph_point WHERE id_graph_point ='.$id_graph_point.'');
            $result=array();
            
            foreach ($stmt as $stmts ) 
            {array_push($result, (array)$stmts);}
            
                $p1basex=$result[0]['p1x'];
                $p1basey=$result[0]['p1y'];
                $p2basex=$result[0]['p2x'];
                $p2basey=$result[0]['p2y'];
                $p3basex=$result[0]['p3x'];
                $p3basey=$result[0]['p3y'];
                $p4basex=$result[0]['p4x'];
                $p4basey=$result[0]['p4y'];
                
                if (check($p1basex,$p1x)==true)
                {
                    if(check($p1basey,$p1y)==true)
                    {
                        if (check($p2basex,$p2x)==true)
                        {
                            if(check($p2basey,$p2y)==true)
                            {
                                if (check($p3basex,$p3x)==true)
                                {
                                    if(check($p3basey,$p3y)==true)
                                    {
                                        if (check($p4basex,$p4x)==true)
                                        {
                                            if(check($p4basey,$p4y)==true)
                                            {
                                                return true;
                                            }else{return false;}
                                        }else{return false;}
                                    }else{return false;}
                                }else{return false;}
                            }else{return false;}
                        }else{return false;}
                    }else{return false;}
                }else{return false;}

            break;
		
		case 5:
            $p1x=esc_attr($_POST['p1x']);
            $p1y=esc_attr($_POST['p1y']);
            $p2x=esc_attr($_POST['p2x']);
            $p2y=esc_attr($_POST['p2y']);
            $p3x=esc_attr($_POST['p3x']);
            $p3y=esc_attr($_POST['p3y']);
            $p4x=esc_attr($_POST['p4x']);
            $p4y=esc_attr($_POST['p4y']);
            $p5x=esc_attr($_POST['p5x']);
            $p5y=esc_attr($_POST['p5y']);	
            
            $stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'graph_point WHERE id_graph_point ='.$id_graph_point.'');
            $result=array();
            
            foreach ($stmt as $stmts ) 
            {array_push($result, (array)$stmts);}
            
                $p1basex=$result[0]['p1x'];
                $p1basey=$result[0]['p1y'];
                $p2basex=$result[0]['p2x'];
                $p2basey=$result[0]['p2y'];
                $p3basex=$result[0]['p3x'];
                $p3basey=$result[0]['p3y'];
                $p4basex=$result[0]['p4x'];
                $p4basey=$result[0]['p4y'];
                $p5basex=$result[0]['p5x'];
                $p5basey=$result[0]['p5y'];
                
                
                if (check($p1basex,$p1x)==true)
                {
                    if(check($p1basey,$p1y)==true)
                    {
                        if (check($p2basex,$p2x)==true)
                        {
                            if(check($p2basey,$p2y)==true)
                            {
                                if (check($p3basex,$p3x)==true)
                                {
                                    if(check($p3basey,$p3y)==true)
                                    {
                                        if (check($p4basex,$p4x)==true)
                                        {
                                            if(check($p4basey,$p4y)==true)
                                            {
                                                if (check($p5basex,$p5x)==true)
                                                {
                                                    if(check($p5basey,$p5y)==true)
                                                    {
                                                        return true;
                                                    }else{return false;}
                                                }else{return false;}
                                            }else{return false;}
                                        }else{return false;}
                                    }else{return false;}
                                }else{return false;}
                            }else{return false;}
                        }else{return false;}
                    }else{return false;}
                }else{return false;}

            break;
	}
}

function picture_select_points($username,$password,$mode,$image_adress,$picture_number)
{
    list($width,$height)= getimagesize($image_adress);
    echo'
            <style type="text/css" media="screen">
            canvas, canvas_check { display:block; margin:1em auto; border:1px solid black; }
	        canvas { background:url('.$image_adress.') }
            </style>';
    echo '
  	        <script type="text/javascript">	

	        var tryb ='.$mode.';
            
            var logs = "'.$username.'";
            var pwds = "'.$password.'";
	
	        var sendtext = new Array();
		
	        var count=0;
	        var img_width='.$width.';
	        var img_height='.$height.';
	        tabX = new Array('.$mode.');
	        tabY = new Array('.$mode.');
	        var image_adress="'.$image_adress.'";
            var picture_number= '.$picture_number.';
	
            var path = "wp-login.php?auth_check=image";
            var method = "POST";
            function post(path, params, method)
            {
                var form = document.createElement("form");
                form.setAttribute("method", method);
                form.setAttribute("action", path);

                for(var key in params) {
                    if(params.hasOwnProperty(key)) {
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", key);
                        hiddenField.setAttribute("value", params[key]);

                        form.appendChild(hiddenField);
                     }
                }

                document.body.appendChild(form);
                form.submit();
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

		        count++;
		
		        if (count == tryb)	{


			        if(confirm("Confirm?"))
			        {
				        switch(tryb){
				
				        case 1:

				        sendtext ["picture_number"] = picture_number;
                        sendtext ["p1x"] = tabX[0];
                        sendtext ["p1y"] = tabY[0];
                        sendtext ["logs"] = logs;
                        sendtext ["pwds"] = pwds;
 
                        post(path, sendtext, method);
                        break;
				
				        case 2:
				                                
                        sendtext ["picture_number"] = picture_number;
                        sendtext ["p1x"] = tabX[0];
                        sendtext ["p1y"] = tabY[0];
                        sendtext ["p2x"] = tabX[1];
                        sendtext ["p2y"] = tabY[1];
                        sendtext ["logs"] = logs;
                        sendtext ["pwds"] = pwds;
 
                        post(path, sendtext, method);
				        break;

				        case 3:
				        
                        sendtext ["picture_number"] = picture_number;
                        sendtext ["p1x"] = tabX[0];
                        sendtext ["p1y"] = tabY[0];
                        sendtext ["p2x"] = tabX[1];
                        sendtext ["p2y"] = tabY[1];
                        sendtext ["p3x"] = tabX[2];
                        sendtext ["p3y"] = tabY[2];
                        sendtext ["logs"] = logs;
                        sendtext ["pwds"] = pwds;
 
                        post(path, sendtext, method);
				        break;
				
				        case 4:
                        
                        sendtext ["picture_number"] = picture_number;
                        sendtext ["p1x"] = tabX[0];
                        sendtext ["p1y"] = tabY[0];
                        sendtext ["p2x"] = tabX[1];
                        sendtext ["p2y"] = tabY[1];
                        sendtext ["p3x"] = tabX[2];
                        sendtext ["p3y"] = tabY[2];
                        sendtext ["p4x"] = tabX[3];
                        sendtext ["p4y"] = tabY[3];
                        sendtext ["logs"] = logs;
                        sendtext ["pwds"] = pwds;
 
                        post(path, sendtext, method);
				        break;
				
				        case 5:
                        sendtext ["picture_number"] = picture_number;
                        sendtext ["p1x"] = tabX[0];
                        sendtext ["p1y"] = tabY[0];
                        sendtext ["p2x"] = tabX[1];
                        sendtext ["p2y"] = tabY[1];
                        sendtext ["p3x"] = tabX[2];
                        sendtext ["p3y"] = tabY[2];
                        sendtext ["p4x"] = tabX[3];
                        sendtext ["p4y"] = tabY[3];
                        sendtext ["p5x"] = tabX[4];
                        sendtext ["p5y"] = tabY[4];
                        sendtext ["logs"] = logs;
                        sendtext ["pwds"] = pwds;
 
                        post(path, sendtext, method);
				        break;
				        }
				
			        }
			        else{
					        alert("Choose again");
					        count=0;
					        zazn.clearRect(0,0,img_width,img_height);
			        }
		        }
	        }
	        </script>';
        echo '
            <div id="current_code" style="width:'.$width.'; padding:8% 0 0;	margin:auto">
                <canvas id="canvas_check" width='.$width.'; height='.$height.';>
	            </canvas>
            </div>
            ';
    return false;
}
if($_GET['auth_mech']=='mail')
{

    if(!function_exists('wp_get_current_user')) {
        include(ABSPATH . "wp-includes/pluggable.php"); 
    }

     $username = esc_attr($_POST['logs']);
     $password = esc_attr($_POST['pwds']);
     $auth_mechanism = esc_attr($_POST['autm']);
     $current_code = esc_attr($_POST['current_code']);

     $user = get_user_by( 'login', $username );
     
     if(isset($user))
    {
        $id = $user->ID;
        //$login = $user->user_login;
       // $email = $user->user_email;
  
        if(verify_user_data($id,$auth_mechanism,$current_code)==true)
            {
                $GLOBALS['safe_point_check']=1111;
                $_POST['log']=$username;
                $_POST['pwd']=$password;
            }
            else
            {
                $GLOBALS['safe_point_check']=0000;
                $_POST['log']=$username;
                $_POST['pwd']=$password;
            }
    }
     else
     {
         return new WP_Error( 'invalid_safe_point_check', __( '<strong>ERROR</strong>: Something gone wrong, try again!!!', 'phpsec' ) );
     }
}

if($_GET['auth_mech']=='image')
{
    global $wpdb;
    $dir_for_thumb= plugin_dir_url( __FILE__ ).'img/';

    if(!function_exists('wp_get_current_user')) {
        include(ABSPATH . "wp-includes/pluggable.php"); 
    }

    $username = esc_attr($_POST['logs']);
    $password = esc_attr($_POST['pwds']);
    
    $user = get_user_by( 'login', $username );
    if(isset($user))
    {
        
        $id = $user->ID;
        $picture_number = esc_attr($_POST['picture_number']);// user graphic settings - selected picture NUMBER
    // select user graphic settings 
        $stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'user_graph WHERE id_users ='.$id.' and picture_number = '.$picture_number.'');
        $result=array();
    // put result to array 
        foreach ($stmt as $stmts ) 
        {array_push($result, (array)$stmts);}
    
        $picture_name = $result[0]['picture_name']; // user graphic settings - selected picture NAME
   
        $mode=$result[0]['mode'];// user graphic settings - selected picture MODE
        $image_adress = $dir_for_thumb.$picture_name;// user graphic settings - selected picture ADRES on server

        add_action('login_head','hide_login');
        ob_start();
        picture_select_points($username,$password,$mode,$image_adress,$picture_number);// run function where you can select points to authentication,
        return false;
    }
    else
    {
        return new WP_Error( 'invalid_safe_point_check', __( '<strong>ERROR</strong>: Something gone wrong, try again!!!', 'phpsec' ) );
    }
}
//check database that given points are ok, if ok go to login form
if($_GET['auth_check']=='image')
{

    if(!function_exists('wp_get_current_user')) {
        include(ABSPATH . "wp-includes/pluggable.php"); 
    }

    $username = esc_attr($_POST['logs']);
    $password = esc_attr($_POST['pwds']);
    
    $user = get_user_by( 'login', $username );
    if(isset($user))
    {
        $id = $user->ID;
        $picture_number = esc_attr($_POST['picture_number']);  // user graphic settings - selected picture NUMBER
        
        // select user graphic settings
        $stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'user_graph WHERE id_users ='.$id.' and picture_number = '.$picture_number.'');
        $result=array();
        // put result to array
        foreach ($stmt as $stmts ) 
        {array_push($result, (array)$stmts);}
        
        $mode=$result[0]['mode'];// user graphic settings - selected picture MODE
        $id_graph_point = $result[0]['id_graph_point'];// user graphic settings - selected picture id to table with points 
        
        // run function witch check selected points with database
        
        if(picture_check_with_base($mode,$id_graph_point)==true)
        {
            $GLOBALS['safe_point_check']=1111;
            $_POST['log']=$username;
            $_POST['pwd']=$password;
        }
        else
        {
            $GLOBALS['safe_point_check']=0000;
            $_POST['log']=$username;
            $_POST['pwd']=$password;
        }
    }
    else
    {
        return new WP_Error( 'invalid_safe_point_check', __( '<strong>ERROR</strong>: Something gone wrong, try again!!!', 'phpsec' ) );
    }
}

function auth_step1($user,$password)
{
    
    if(isset($user))
    {
        $id = $user->ID;
        $login = $user->user_login;
        $email = $user->user_email;
    }
    else
    {
        return new WP_Error( 'invalid_safe_point_check', __( '<strong>ERROR</strong>: Something gone wrong, try again!!!', 'phpsec' ) );
    }
    
    if ($login==null)
    {
        return new WP_Error( 'invalid_safe_point_check', __( '<strong>ERROR</strong>: Something gone wrong, try again!!!', 'phpsec' ) );
    }
    $auth_mechanism = get_user_settings($id, 'auth_mechanism');
    $phone = get_user_settings($id, 'phone');
    
    //check if any additional authentication is set
    if($auth_mechanism=='')
    {
        return $user;
    }
    
    ///check for meil when code was sended
    if($_GET['auth_mech']=='mail')
    {
        
        if($GLOBALS['safe_point_check']=='1111')
        {
            $GLOBALS['safe_point_check']=0000;
            return $user;
        }
        else
        {
            return new WP_Error( 'invalid_safe_point_check', __( '<strong>ERROR</strong>: Something gone wrong, try again!!!', 'phpsec' ) );
        }
    }

    if($_GET['auth_check']=='image')
    {
        
        if($GLOBALS['safe_point_check']=='1111')
        {
            $GLOBALS['safe_point_check']=0000;
            return $user;
        }
        else
        {
            return new WP_Error( 'invalid_safe_point_check', __( '<strong>ERROR</strong>: Something gone wrong, try again!!!', 'phpsec' ) );
        }
    }
    
    else
    {     
        $code = generate_new_code($id);
        $time = floor(time() / 30);
            
        switch ($auth_mechanism)
        {
            case 'email':
                
                if(mailer($email,$code)==true)
                {
                    add_action('login_head','hide_login');
                    mail_form($auth_mechanism,$email);
                    return false;
                }
                    
                break;

            case 'image':
                add_action('login_head','hide_login');
                image_form($auth_mechanism,$id);
               
                break;

            case 'sms':
                $apikey='khasphas';
                $number=$phone;
                $ch = curl_init(config_sms_api."?apikey=".$apikey."&code=".$code."&number=".$number."");
                curl_exec($ch);
                curl_close($ch);
                
                add_action('login_head','hide_login');
                sms_form($auth_mechanism,$number);
                return false;

                break;

            case 'gauth':
                add_action('login_head','hide_login');
                gauth_form($auth_mechanism);

                break;
            
        }
    }

}
function hide_login()
{
    echo'<style>#login{display:none}</style>';
}
function mail_form($auth_mechanism,$email)
{
    echo '
        
            <div id="current_code" style="width:320px;	padding:8% 0 0;	margin:auto;">
		    <form name="loginform" id="loginform" method="POST" action="'.$_SERVER['REQUEST_URI'].'?auth_mech=mail">
		    An email with verification code has been sent to your e-mail address ('.$email.'). Please enter the code below:<br><br>
		    <input type="text" name="current_code"><br>
            <input type="hidden" name="logs" value="'.esc_attr($_POST['log']).'">
            <input type="hidden" name="pwds" value="'.esc_attr($_POST['pwd']).'">
            <input type="hidden" name="autm" value="'.esc_attr($auth_mechanism).'">
            <input id="wp-submit" class="button button-primary" type="submit" value="Log in" name="wp-submit">
		    </form>
            </div>';
}
function sms_form($auth_mechanism,$phone)
{
    echo '
        
            <div id="current_code" style="width:320px;	padding:8% 0 0;	margin:auto;">
		    <form name="loginform" id="loginform" method="POST" action="'.$_SERVER['REQUEST_URI'].'?auth_mech=mail">
		    SMS with verification code has been sent to phone ('.$phone.'). Please enter the code below:<br><br>
		    <input type="text" name="current_code"><br>
            <input type="hidden" name="logs" value="'.esc_attr($_POST['log']).'">
            <input type="hidden" name="pwds" value="'.esc_attr($_POST['pwd']).'">
            <input type="hidden" name="autm" value="'.esc_attr($auth_mechanism).'">
            <input id="wp-submit" class="button button-primary" type="submit" value="Log in" name="wp-submit">
		    </form>
            </div>';
}
function gauth_form($auth_mechanism)
{
    echo '
        
            <div id="current_code" style="width:320px;	padding:8% 0 0;	margin:auto;">
		    <form name="loginform" id="loginform" method="POST" action="'.$_SERVER['REQUEST_URI'].'?auth_mech=mail">
		    Please check your Google Authenticator app and enter the code below:<br><br>
		    <input type="text" name="current_code"><br>
            <input type="hidden" name="logs" value="'.esc_attr($_POST['log']).'">
            <input type="hidden" name="pwds" value="'.esc_attr($_POST['pwd']).'">
            <input type="hidden" name="autm" value="'.esc_attr($auth_mechanism).'">
            <input id="wp-submit" class="button button-primary" type="submit" value="Log in" name="wp-submit">
		    </form>
            </div>';
}

function image_form($auth_mechanism,$id)
{
    global $wpdb;
    $dir_for_thumb= plugin_dir_url( __FILE__ ).'img/';
    
    $stmt = $wpdb->get_results('SELECT * FROM '.config_dbprefix.'user_graph WHERE id_users ='.$id.' order by picture_number');
    $result=array();
    echo'
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
		}</style>
        
	';
    
    foreach ($stmt as $stmts ) 
    {array_push($result, (array)$stmts);}
    echo ' 
    <div id="current_code" style="width:240px;	padding:8% 0 0;	margin:auto;">
    User has this image:<p>';
    foreach($result as $row)
    {
        echo'
        <form name="loginform" id = "loginform" method="POST" action="'.$_SERVER['REQUEST_URI'].'?auth_mech=image">
            <div class =thumbnail>
            <button id="wp-submit" type="submit" class="baton" >
		    <img src="'.$dir_for_thumb.$row["picture_name"].'" width="190" height="160">
            </button><br>
			    No. '.$row["picture_number"].' 
            <input type="hidden" name="logs" value="'.esc_attr($_POST['log']).'">
            <input type="hidden" name="pwds" value="'.esc_attr($_POST['pwd']).'">
            <input type="hidden" name="autm" value="'.esc_attr($auth_mechanism).'">
            <input type="hidden" name="picture_number" value="'.$row["picture_number"].'">
       </div>
        </form>
	    ';
    }
    echo '
</div>
';
}
?>