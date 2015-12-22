<?php
include_once 'casconnect.php';
include_once 'dbconnect.php';

// logout if desired
if (isset($_REQUEST['logout'])) {
	phpCAS::logout();
}

if (isset($_POST['btn-signup'])) {
	$firstname = ($_POST['firstname']);
	$lastname = ($_POST['lastname']);
	$username = ($_POST['username']);
	$a = array();
	$array = serialize($a); 
	$onid = phpCAS::getUser();
	$userlevel = 0;
	$mysqli->query("INSERT INTO users(firstname, lastname, username, onid, achievements, userlevel) VALUES('$firstname', '$lastname', '$username', '$onid', '$array', '$userlevel')");
}
// for this test, simply print that the authentication was successfull
?>
<html>
  <head>
    <title>Collaboratory Achievement Management</title>
</head>
  <body>
    <h1>Successfull Authentication!</h1>
    <p>the user's login is <b><?php echo phpCAS::getUser(); ?></b>.</p>
<?php
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
?>
</body>
</html>
