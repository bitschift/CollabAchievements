<?php
include_once 'casconnect.php';
include_once 'dbconnect.php';

// logout if desired
if (isset($_REQUEST['logout'])) {
	phpCAS::logout();
}

if (isset($_POST['btn-signup'])) {
	$name = mysql_real_escape_string($_POST['name']);
	$email = mysql_real_escape_string($_POST['email']);
	$project = mysql_real_escape_string($_POST['project']);

	mysql_query("INSERT INTO Employees(name, email, project, onid) VALUES('$name', '$email', '$project', '$onid')");
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
$x = mysql_query("SELECT * FROM Employees WHERE onid='$onid'");
$y = mysql_num_rows($x);
if($y == 0) {
	echo '<p>Welcome! We need you to register for the first time.</p>
		<center>
		<div id="login-form">
		<form method="post">
		<table align="center" width="30%" border="0">
		<tr>
		<td><input type="text" name="name" placeholder="Your Name" required /></td>
		</tr>
		<tr>
		<td><input type="text" name="email" placeholder="Prefered Email" required /></td>
		</tr>
		<tr>
		<td><input type="text" name="project" placeholder="Project" required /></td>
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
