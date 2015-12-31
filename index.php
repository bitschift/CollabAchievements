<?php
include_once 'casconnect.php';
include_once 'dbconnect.php';
include_once 'phpfunctions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// logout if desired
if (isset($_REQUEST['logout'])) {
	phpCAS::logout();
}

if (isset($_REQUEST['btn-signup'])) {
	$firstname = mysqli_real_escape_string($mysqli, $_REQUEST['firstname']);
	$lastname = mysqli_real_escape_string($mysqli, $_REQUEST['lastname']);
	$username = mysqli_real_escape_string($mysqli, $_REQUEST['username']);
	$onid = phpCAS::getUser();
	
	if (isset($_REQUEST['firstname']) AND isset($_REQUEST['lastname']) AND isset($_REQUEST['username'])){
		$mysqli->query("INSERT INTO users(firstname, lastname, username, onid, userlevel, hash) VALUES('$firstname', '$lastname', '$username', '$onid', 0,'".randomhash()."')");
		echo '<html>
			<head>
			<title>Achievements</title>
			</head>
			<body>
			<h1>Successful Authentication!</h1>
			<p>the user\'s login is <b>' . phpCAS::getUser() . '</b>.</p>';
		echo '<meta http-equiv="refresh" content="0; url=home.php" />';
	}
}

if (isset($_REQUEST['reviewhash'])){
			echo '<html>
			<head>
			<title>Achievements</title>
			</head>
			<body>
			<h1>This will be a spot to review another person\'s work.</h1>
			<p>Your user login is <b>' . phpCAS::getUser() . '</b>.</p>';
			
} else if (isset($_REQUEST['requesthash'])){
	echo '';
	
} else {
	$onid = phpCAS::getUser();
	$x = $mysqli->query("SELECT * FROM users WHERE onid='$onid'");
	$y = $x->num_rows;
	if($y == 0) {
		echo '<p>Welcome! We need you to register for the first time.</p>
		<center>
		<div id="login-form">
		<form method="post">
		<table align="center" width="30%" border="0">
		<tr>
		<td><input type="text" name="firstname" placeholder="First Name" required /></td>
		</tr>
		<tr>
		<td><input type="text" name="lastname" placeholder="Last Name" required /></td>
		</tr>
		<tr>
		<td><input type="text" name="username" placeholder="Username" required /></td>
		</tr>
		<tr>
		<td><button type="submit" name="btn-signup">Register</button></td>
		</tr>
		</table>
		</form>
		</div>
		<p><a href="?logout=">Logout</a></p>
		</center>';
	} else {
		echo '<meta http-equiv="refresh" content="0; url=home.php" />';
	}
}
?>
</body>
</html>
