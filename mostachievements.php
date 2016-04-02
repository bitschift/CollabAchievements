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

$termBegin = '2016-03-23';
$termEnd = '2016-06-15';
$termName = 'Spring 2016';
?>

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

</div>

</body>
</html>
