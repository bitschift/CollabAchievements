<?php
include_once 'dbconnect.php';
include_once 'phpfunctions.php';

echo '<!DOCTYPE html>
	<html>
	<head>
	<title>Collaboratory Achievement Management</title>
	</head>';
?>

<!-- Bootstrap -->
<link href="./css/bootstrap.min.css" rel="stylesheet">
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins -->
<script src="./js/bootstrap.min.js"></script>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$termBegin = '2016-01-03';
$termEnd = '2016-03-10';
$termName = 'Winter 2016';
?>

<nav class="navbar navbar-inverse navbar-fixed-top">
	<a class="navbar-brand" href="http://www.oregonstate.edu">Oregon State University</a>
	<div style="padding-right:1%;">
		<a href="./home.php?login"><button type="button" class="btn btn-default navbar-btn pull-right">Sign in</button></a>
	</div>
</nav>
<div class='row'>
<div style='padding-top:5em;' class='col-sm-8 col-sm-offset-2'>
<center><h3>The Mastery Challenge</h3></center>
</div></div>
<div class='row'><div class='col-sm-4 col-sm-offset-2'>
<p>
Welcome to the Mastery Challenge, a program designed to  showcase your abilities and challenge you to learn something new. 
To begin log in above. 
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
  <div class="panel-heading">Most Achievements (<?php echo $termName;?>)</div>
  <table class="table">
<?php
$query = "SELECT userid, COUNT(*) as count FROM achievements WHERE issuedDate > STR_TO_DATE('$termBegin','%Y-%m-%d') AND issuedDate < STR_TO_DATE('$termEnd','%Y-%m-%d') GROUP BY userid ORDER BY count DESC LIMIT 10";
//echo $query;
$requestRes = $mysqli->query($query);
$i = 0;
while ($requestRow = $requestRes->fetch_array(MYSQLI_ASSOC)) {
	$requesterid = $requestRow['userid'];
	$userRes = $mysqli->query("SELECT * FROM users WHERE id='$requesterid'");
	$userRow = $userRes->fetch_array(MYSQLI_ASSOC);
	echo '<tr><td>' . ($i+1) . ': ' . $userRow['username'] . '</td><td><p class="text-right" style="margin: 0;padding: 0;">' . $requestRow['count'] . ' Achievements</p></td></tr>';
	$i++;
}

?>
</table>
</div>

<div class="panel panel-default" style="width:100%;">
  <div class="panel-heading">Most Achievements</div>
  <table class="table">
<?php
$requestRes = $mysqli->query("SELECT userid, COUNT(userid) as count FROM achievements GROUP BY userid ORDER BY count DESC LIMIT 10");
for ($i=0;$i<10;$i++) {
	$requestRow = $requestRes->fetch_array(MYSQLI_ASSOC);
	$requesterid = $requestRow['userid'];
	$userRes = $mysqli->query("SELECT * FROM users WHERE id='$requesterid'");
	$userRow = $userRes->fetch_array(MYSQLI_ASSOC);
	echo '<tr><td>' . ($i+1) . ': ' . $userRow['username'] . '</td><td><p class="text-right" style="margin: 0;padding: 0;">' . $requestRow['count'] . ' Achievements</p></td></tr>';
}

?>
</table>
</div>

</div>
</div>
</body>
</html>
