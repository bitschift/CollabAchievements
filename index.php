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
The School of Electrical Engineering and Computer Science at Oregon State University is initiating a new program to create more hands-on learning opportunities for students. The Mastery Challenge program [make this a link to webpage] is based on a concept called gamification, which uses elements of game playing such as leader boards and badges, to motivate participants to gain new abilities such as 3D modeling and Python programming. 
“The program is designed to help students apply the knowledge they learn in classes to practical skills they will need for jobs when they graduate,” said Don Heer, instructor of electrical and computer engineering. </p>
<p>Experiential learning is a focus for Heer who has also created the TekBots program which integrates course content with building a robot, and the CreateIT Collaboratory for students to work with outside clients to create prototypes.</p>
<p>To participate, students login to the Mastery Challenge website with their university account to see the list of challenges for which they can earn achievements. Participants can work on their own, or get help by contacting students who already have that achievement. Prizes will be awarded to students with the highest number of achievements each term. Helping other participants is another way for students to earn achievements.</p>
Peers also participate in the evaluation process. To earn an achievement, a participant must demonstrate their ability by uploading a video or document to the website for review. Students who already have that achievement can recommend to Heer if the application should be accepted or denied. Heer then makes the final decision.</p>
<p>The Mastery Challenge program is open to anyone at Oregon State — students from other majors and faculty and staff can participate. Heer is starting with abilities in electrical engineering and computer science, but his vision is that the program will expand across the university, so students can earn abilities in a wide variety of disciplines.</p>
Questions about the program can be directed to Don Heer. 

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
