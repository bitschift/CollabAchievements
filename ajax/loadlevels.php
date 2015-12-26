<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//require_once ('../includes/phpfunctions.php');
//include_once '../casconnect.php';
include_once '../dbconnect.php';

if (isset($_REQUEST['giveachievement']) && $_REQUEST['giveachievement'] != 0){
	$giveachievement = mysqli_real_escape_string($mysqli, $_REQUEST['giveachievement']);

	$query = "SELECT * FROM levels WHERE achievementid = $giveachievement ORDER BY level ASC";
	//echo $query . '<BR>';
	$result = $mysqli->query($query);
	$i=1;
	while ($row = $result->fetch_assoc()){
		echo '<option value="'.$i.'">Level '.$i.'</option>'; 
		$i++;
		
	}
} 

if (isset($_REQUEST['requestachievement']) && $_REQUEST['requestachievement'] != 0){
	$requestachievement = mysqli_real_escape_string($mysqli, $_REQUEST['requestachievement']);

	$query = "SELECT * FROM levels WHERE achievementid = $requestachievement ORDER BY level ASC";
	//echo $query . '<BR>';
	$result = $mysqli->query($query);
	$i=1;
	while ($row = $result->fetch_assoc()){
		echo '<option value="'.$i.'">Level '.$i.'</option>'; 
		$i++;
		
	}
}
