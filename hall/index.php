<?php
include_once '../dbconnect.php';
include_once '../phpfunctions.php';

echo '<!DOCTYPE html>
	<html>
	<head>
	<title>Collaboratory Achievement Management</title>
	</head>';
?>

<!-- Bootstrap -->
<link href="../css/bootstrap.min.css" rel="stylesheet">
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins -->
<script src="../js/bootstrap.min.js"></script>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<div class='row'>
<div style='padding-top:5em;' class='col-sm-8 col-sm-offset-2'>
<center><h3>The Mastery Challenge</h3></center>
</div></div>
<div class='row'><div class='col-sm-4 col-sm-offset-2'>
<p>
Welcome to the Mastery Challenge, a program designed to  showcase your abilities and challenge you to learn something new. 
<ul><li>Login using your Oregon State University ONID account. Currently, only OSU students or employees can participate.
<li>First time users will need to create an account including a username of your choice.  
<li>Choose from a wide variety of achievements to start working on. You can choose several first level achievements to show your broad knowledge and skills, or dive deep into one achievement and reach its highest level. 
<li>When you feel you have mastered a level of an achievement, make a short video about it and submit it for review. Your peers will look over your submission and recommend a decision to the Mastery Challenge Gurus. 
<li>Each term we will have prizes for the most levels achieved in a term and for various other challenges each term!
<ul></p>

<h2>Winter 2016: $150 Cash Prize to the most achievements this term</h2>
<h2>Winter 2016: $150 Cash Prize for the most level 2 and higher achievements this term</h2>
<h2>Winter 2016: If you earn even one achievement this term, you will be entered into a drawing for a $50 prize.</h2>
</div>

<div class='col-sm-4'>

<div class="panel panel-default" style="width:100%;">
	<div class="panel-heading">Who's Available</div>
	<table class="table">
<?php
$query = "SELECT * FROM clock";
$clockRes = $mysqli->query($query);
for ($i=0; $i<$clockRes->num_rows; $i++) {
	$clockRow = $clockRes->fetch_array(MYSQLI_ASSOC);
	$userid = $clockRow['userid'];
	$query = "SELECT * FROM users WHERE id='$userid'";
	$empRes = $mysqli->query($query);
	$empRow = $empRes->fetch_array(MYSQLI_ASSOC);
	echo '<tr><td>' , $empRow['firstname'], ' ', $empRow['lastname'], ' is in room ', $clockRow['roomid'], '</tr></td>';
} ?>
