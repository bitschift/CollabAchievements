<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//require_once ('../includes/phpfunctions.php');
//include_once '../casconnect.php';
include_once '../dbconnect.php';

if (isset($_REQUEST['requestachievement']) && $_REQUEST['requestachievement'] != 0){
	$requestachievement = mysqli_real_escape_string($mysqli, $_REQUEST['requestachievement']);
}
if (isset($_REQUEST['requestlevel']) && $_REQUEST['requestlevel'] != 0){
	$requestlevel = mysqli_real_escape_string($mysqli, $_REQUEST['requestlevel']);
}

if (isset($requestlevel) AND isset($requestachievement)){
	$query = "SELECT levels.*, achievementList.info AS achievementinfo, achievementList.name FROM levels INNER JOIN achievementList ON achievementList.id = levels.achievementid WHERE levels.achievementid = $requestachievement ORDER BY level ASC";
	//echo $query . '<BR>';
	$result = $mysqli->query($query);
	$row = $result->fetch_assoc();
	echo '<h3>' . $row['name'] . '</h3><p>' . $row['achievementinfo'] . '</p>';
	if ($row['level'] == $requestlevel)
		echo '<p style="margin-left:5em;margin-right:5em;"><strong>Level: '.$row['level'].'<BR>' . nl2br($row['info']) . '</strong></p>';
	else
		echo '<p style="margin-left:5em;margin-right:5em;">Level: '.$row['level'].'<BR>' . nl2br($row['info']) . '</p>';
	while ($row = $result->fetch_assoc()){
		if ($row['level'] == $requestlevel)
			echo '<p style="margin-left:5em;margin-right:5em;"><strong>Level: '.$row['level'].'<BR>' . nl2br($row['info']) . '</strong></p>';
		else
			echo '<p style="margin-left:5em;margin-right:5em;">Level: '.$row['level'].'<BR>' . nl2br($row['info']) . '</p>';
	}
} 

if (isset($_REQUEST['giveachievement']) && $_REQUEST['giveachievement'] != 0){
	$giveachievement = mysqli_real_escape_string($mysqli, $_REQUEST['giveachievement']);
}
if (isset($_REQUEST['givelevel']) && $_REQUEST['givelevel'] != 0){
	$givelevel = mysqli_real_escape_string($mysqli, $_REQUEST['givelevel']);
}

if (isset($givelevel) AND isset($giveachievement)){
	$query = "SELECT levels.*, achievementList.info AS achievementinfo, achievementList.name FROM levels INNER JOIN achievementList ON achievementList.id = levels.achievementid WHERE levels.achievementid = $giveachievement ORDER BY level ASC";
	//echo $query . '<BR>';
	$result = $mysqli->query($query);
	$row = $result->fetch_assoc();
	echo '<h3>' . $row['name'] . '</h3><p>' . $row['achievementinfo'] . '</p>';
	if ($row['level'] == $givelevel)
		echo '<p style="margin-left:5em;margin-right:5em;"><strong>Level: '.$row['level'].'<BR>' . nl2br($row['info']) . '</strong></p>';
	else
		echo '<p style="margin-left:5em;margin-right:5em;">Level: '.$row['level'].'<BR>' . nl2br($row['info']) . '</p>';
	while ($row = $result->fetch_assoc()){
		if ($row['level'] == $givelevel)
			echo '<p style="margin-left:5em;margin-right:5em;"><strong>Level: '.$row['level'].'<BR>' . nl2br($row['info']) . '</strong></p>';
		else
			echo '<p style="margin-left:5em;margin-right:5em;">Level: '.$row['level'].'<BR>' . nl2br($row['info']) . '</p>';
	}
} 
