<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include_once '../casconnect.php';
include_once '../dbconnect.php';
include_once '../phpfunctions.php';


$onid = phpCAS::getUser();
$res = $mysqli->query("SELECT * FROM users WHERE onid = '$onid'");
$userrow = $res->fetch_array(MYSQLI_ASSOC);		//keep an array of elements in the user's table for easy access

if (isset($_REQUEST['logout'])) {
	phpCAS::logoutWithRedirectService('http://eecs.oregonstate.edu/education/achievements');
}

echo '<!DOCTYPE html>
	<html>
	<head>
	<title>Collaboratory Achievement Management</title>
	</head>';
?>

	<!-- Bootstrap -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../js/bootstrap.min.js"></script>
	
	<script src="../js/sorttable.js"></script>
	

<?php
echo '<body>';

if (isset($_REQUEST['btn-send'])) {
	$userlevel = $mysqli->real_escape_string($_REQUEST['userlevel']);
	$body = $mysqli->real_escape_string($_REQUEST['body']);
	$body = stripslashes(stripslashes(htmlspecialchars_decode(str_replace(array('\r\n', '\r', '\n'), "", $body))));
	$subject = $mysqli->real_escape_string($_REQUEST['subject']);
	$query = "SELECT * FROM users WHERE userlevel >= $userlevel ORDER BY onid ASC";
	$result = $mysqli->query($query);
	//echo $query . '<BR>';
	while ($row = $result->fetch_assoc()){
		//Send the email/message
		email_message($subject, $row['onid']. '@oregonstate.edu', $body);
	}
}

if (isset($_REQUEST['btn-test'])) {
	$body = $mysqli->real_escape_string($_REQUEST['body']);
	$body = stripslashes(stripslashes(htmlspecialchars_decode(str_replace(array('\r\n', '\r', '\n'), "", $body))));
	$subject = $mysqli->real_escape_string($_REQUEST['subject']);
	email_message($subject, $userrow['onid']. '@oregonstate.edu', $body);
}

?>

<nav class="navbar navbar-inverse navbar-fixed-top">
		<a class="navbar-brand" href="http://www.oregonstate.edu">Oregon State University</a>
		<div style="padding-right:1%;">
<?php
if (isset($onid)){
	echo '<div class="navbar-brand pull-right" style="padding-right:1%;"><span class="glyphicon glyphicon-user"></span> <a href="../profile.php">Account (' . $onid . ')</a> - <a href="' . $_SERVER['PHP_SELF'] . '?logout">Logout</a> - <a href="../home.php">Home</a></div>';
} else {
	echo '<a href="' . $_SERVER['PHP_SELF'] . '?login"><button type="button" class="btn btn-default navbar-btn pull-right">Sign in</button></a>';
}
?>
		</div>
</nav>

<?php

if ($userrow['userlevel'] > 2){
	echo '<div class="row" style="padding-top:2em;"><div style="padding-top:5em;" class="col-sm-10 col-sm-offset-1">';
	echo '<form action="./messageusers.php" method="post">';
	echo 'Subject: <input type="text" name="subject" id="subject" value="' . (isset($subject) ? $subject : ''). '" title="Please enter message subject."><BR>';
	echo 'User Level: <select name="userlevel" title="Please select group to send to."><option value="0">New</option><option value="1">Normal</option><option value="2">Reviewers</option><option value="3">Approvers</option></select><BR>';
	echo 'Message Body:<textarea cols="40" rows="5" name="body" title="Please enter the message to be sent. Simple html is ok.">' . html_entity_decode((isset($body) ? $body : "")). '</textarea><BR>';
	echo '<input type="submit" value="Send to All" name="btn-send">';
	echo '<input type="submit" value="Send Test to me" name="btn-test">';
	echo '</form></div></div>';
} else {
	echo 'You do not belong here';
}
?>
</body>
</html>
