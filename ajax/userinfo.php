<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//require_once ('../includes/phpfunctions.php');
include_once '../casconnect.php';
include_once '../dbconnect.php';

$onid = phpCAS::getUser();
$res = $mysqli->query("SELECT * FROM users WHERE onid='$onid'");
$userrow = $res->fetch_array(MYSQLI_ASSOC);		//keep an array of elements in the user's table for easy access
	
	if (isset($_REQUEST['payment_method']) && $_REQUEST['payment_method'] != 0){
		$payment_method = mysqli_real_escape_string($mysqli, check_input($_REQUEST['payment_method']));
	} else {
		echo '<h2>You need to select a payment method</h2>';
		exit();
	}

if ($payment_method == 3){
	phpCAS::client(SAML_VERSION_1_1,'login.oregonstate.edu',443,'cas');
	phpCAS::setNoCasServerValidation();
	phpCAS::forceAuthentication();
	//$t = phpCAS::getAttributes();
	//print_r($t);
	
	$service = $url = strtok(phpCAS::getServiceURL(),'?');
	if(strtolower(phpCAS::getUser()) != NULL) //Logged in
		echo 'User: ' . strtolower(phpCAS::getUser());
	else
		echo 'User needs to login';
} else if ($payment_method == 1){
	echo "<div class='row'><div class='col-sm-8 col-sm-offset-2'><strong>When paying by credit card...</strong></div></div>
	<div class='row' style='padding-top:1%;'><div class='col-sm-4 col-sm-offset-2'><b>Your Email:</b> (Must be valid to confirm order)</div>
<div class='col-sm-4'><input class='fi form-control' type='email' pattern='[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$' name='email'></div></div>
<div class='row' style='padding-top:1%;'><div class='col-sm-4 col-sm-offset-2'><b>First Name:</b></div>
<div class='col-sm-4'><input class='fi form-control' type=text size=55 name='firstname'></div></div>
<div class='row' style='padding-top:1%;'><div class='col-sm-4 col-sm-offset-2'><b>Last Name:</b></div>
<div class='col-sm-4'><input class='fi form-control' type=text size=55 name='lastname'></div></div>";
} else if ($payment_method == 2){
	echo "<div class='row' style='padding-top:1%;'><div class='col-sm-4 col-sm-offset-2'><b>Your Email:</b> (Must be valid to confirm order)</div>
<div class='col-sm-4'><input class='fi form-control' type='email' pattern='[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$' name='email'></div></div>
<div class='row' style='padding-top:1%;'><div class='col-sm-4 col-sm-offset-2'><b>First Name: </b></div>
<div class='col-sm-4'><input class='fi form-control' type=text size=55 name='firstname'></div></div>
<div class='row' style='padding-top:1%;'><div class='col-sm-4 col-sm-offset-2'><b>Last Name: </b></div>
<div class='col-sm-4'><input class='fi form-control' type=text size=55 name='lastname'></div></div>
<div class='row' style='padding-top:1%;'><div class='col-sm-4 col-sm-offset-2'><b>OSU Grant Account Code: </b></div>
<div class='col-sm-4'><input class='fi form-control' type=text size=55 name='account'></div></div>";
}

