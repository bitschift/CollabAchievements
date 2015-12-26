<?php
include_once 'header.php';


echo '<body>';

if(isset($_REQUEST['btn-request'])) {
	$achievement = $_REQUEST['requestachievement'];
	$level = $_REQUEST['requestlevel'];
	$userAch = unserialize($userrow['achievements']);
	$userid = $userrow['id'];

	$levelRes = $mysqli->query("SELECT * FROM levels WHERE achievementid='$achievement' && level='$level'");
	$levelRow = $levelRes->fetch_array(MYSQLI_ASSOC);
	
	for($i=0;$i<count($userAch);$i++) {
		if($userAch[$i] == $achievement) {
?>
			<script>alert('Already have achievement');</script>
<?php	}
	}
	
	$reqAch = $levelRow['id'];
	$query = "SELECT * FROM achievementList WHERE id = $achievement";
	$result = $mysqli->query($query);
	$row = $result->fetch_assoc();
	$userrow['name'] = $row['name'];
	$userrow['level'] = $level;
	
	$query = "SELECT * FROM requests WHERE requesterid = '$userid' AND achievementid = '$reqAch' AND status = 0";
	$result = $mysqli->query($query);
	if ($result->num_rows > 0) {//Already Under Review
		echo "<script>alert('You have already requested to be reviewed for level $level of the " . $userrow['name'] . " achievement. Please wait for the that review to complete.');</script>";
	} else{
	$mysqli->query("INSERT INTO requests(requesterid, achievementid, hash) VALUES('$userid', '$reqAch', '".randomhash()."')");
	
	
	$query = "SELECT * FROM achievementList WHERE id = $achievement";
	$result = $mysqli->query($query);
	$row = $result->fetch_assoc();
	$userrow['name'] = $row['name'];
	$userrow['level'] = $level;
	
	email_message('Achievement Request', $userrow['onid'] . '@oregonstate.edu', create_message('./emails/request.eml', $userrow));
	}
	
}

if(isset($_REQUEST['btn-give'])) {
	$achievement = $_REQUEST['giveachievement'];
	$level = $_REQUEST['givelevel'];
	$employeeid = $_REQUEST['employee'];	
	$empRes = $mysqli->query("SELECT * FROM users WHERE id='$employeeid'");
	//echo $query .'<BR>';
	$empRow = $empRes->fetch_array(MYSQLI_ASSOC);
	$empAch = array();
	$empAch = unserialize($empRow['achievements']);

	$levelRes = $mysqli->query("SELECT * FROM levels WHERE achievementid='$achievement' && level='$level'");
	$levelRow = $levelRes->fetch_array(MYSQLI_ASSOC);

	//query to erase a lower level if one exists
	$lowerRes = $mysqli->query("SELECT * FROM levels WHERE achievementid='$achievement'");
	$lowerRow = $lowerRes->fetch_array(MYSQLI_ASSOC);

	$done = false;
	for($i=0;$i<count($empAch);$i++) {
		if($empAch[$i] == $levelRow['id']) {	
?>
			<script>alert('Already have achievement');</script>
<?php		$done = true;
			}
		// remove previous levels
	}

	if ($done == false)
		$empAch[] = $levelRow['id'];
	$serialized = serialize($empAch);
	$mysqli->query("UPDATE users SET achievements='$serialized' WHERE id=".$_REQUEST['employee']);
}

if(isset($_REQUEST['btn-endorse'])) { //This is to be reworked/removed soon
	$achievement = $_REQUEST['achievement'];
	$empRes = $mysqli->query("SELECT * FROM users WHERE id=".$_REQUEST['employee']);
	$empRow = $empRes->fetch_array(MYSQLI_ASSOC);
	$empAch = array();
	$empAch = unserialize($empRow['achievements']);
	$empid = $empRow['id'];
	$userid = $userrow['id'];

	$levelRes = $mysqli->query("SELECT * FROM levels WHERE achievementid='$achievement' && level='$level'");
	$levelRow = $levelRes->fetch_array(MYSQLI_ASSOC);
	
	for($i=0;$i<count($empAch);$i++) {
		for($y=0;$y<count($achievements);$y++) {
			if($empAch[$i] == $achievements[$y]) {
?>
				<script>alert('Already have achievement');</script>
<?php		}
		}
	}

	$reqAch = $levelRow['id'];
	$mysqli->query("INSERT INTO requests(requesterid, achievementid, committeeids) VALUES('$empid', '$reqAch', '$userid')");	
	
}

$ser = $userrow['achievements'];
$dest = unserialize($ser);
$cnt = count($dest);
?>

<nav class="navbar navbar-inverse navbar-fixed-top">
		<a class="navbar-brand" href="http://www.oregonstate.edu">Oregon State University</a>
		<div style="padding-right:1%;">
		<?php
		if (isset($onid)){
			echo '<div class="navbar-brand pull-right" style="padding-right:1%;"><span class="glyphicon glyphicon-user"></span>' . $onid . ' - <a href="' . $_SERVER['PHP_SELF'] . '?logout">Logout</a></div>';
		} else {
			echo '<a href="' . $_SERVER['PHP_SELF'] . '?login"><button type="button" class="btn btn-default navbar-btn pull-right">Sign in</button></a>';
		}
		?>
		</div>
	</nav>
<div class='row'><div style="padding-top:5em;" class='col-sm-1 col-sm-offset-1'><h3>Your Achievements</h3></div><div style="padding-top:5em;" class='col-sm-6' id='myachievements'>
<?php
$myachievements = achievementlist($mysqli, $onid);
if (empty($myachievements)){
	echo '<div class="col-sm-4"><h3>No Achievements Found.</h3></div>';
} else{
	foreach ($myachievements AS $achievement){
		echo '<div class="col-sm-2 thumbnail" style="border-style:none;"><img class="img-responsive" style="width:100%;display:block;" src="./img/'.$achievement['image'].'" title="'.$achievement['name'].' - Level '.$achievement['level'].'"></div>';
	}
}
?>
</div><div style="margin-top:5em;background-color:#f2f2f2;border-radius:10px;" class='col-sm-2'>
<form method="post" class="form-group"><label for="requestachievement">Request achievement:</label>
<select name="requestachievement" id="requestachievement" class="form-control" onchange="loadrequestlevels()"><option>Choose ...</option>
<?php
		$achieve = $mysqli->query("SELECT * FROM achievementList");
	$ach_length = $achieve->num_rows;

	$empl = $mysqli->query("SELECT * FROM users");
	$emplength = $empl->num_rows;

	for($i=0;$i<$ach_length;$i++) {
		$achRow = $achieve->fetch_array(MYSQLI_ASSOC);
		echo '<option value="', $achRow['id'], '">', $achRow['name'], '</option>';
	}
?>
</select><select name="requestlevel" id="requestlevel" class="form-control" onchange="loadrequestachievementinfo()"></select>
<input type="submit" name="btn-request" value="Submit" class="form-control"></button></form>
</div></div>

<script>
	function loadrequestachievementinfo(){
		requestachievement = $('#requestachievement').val();
		requestlevel = $('#requestlevel').val();
		$.ajax({
		type: 'GET',
		url: './ajax/loadachievementinfo.php',
		dataType: 'html',
		data: {	requestlevel: requestlevel,
				requestachievement: requestachievement},
		success: function(result)
			{
			$('#requestachievementinfo').html(result);
			}
		});
	}
</script>



<div class='row'><div style="padding-top:3%;" class='col-sm-8 col-sm-offset-2' id='requestachievementinfo'>
</div></div>





<?php
if ($userrow['userlevel'] > 1){
	echo "<div class='row'><div style='padding-top:3%;' class='col-sm-1 col-sm-offset-1'><h3>Requests to Review</h3></div><div class='col-sm-8' id='myreviews'>";
	$requests = requestslist($mysqli, 'heer');
	if (empty($requests)){
		echo '<h2>You do not have any open requests to review</h2>';
	} else {
	foreach($requests as $request){
		//echo '<div class="col-sm-2 thumbnail"><img class="img-responsive" style="width:100%;display:block;" src="./img/'.$achievement['image'].'" title="'.$achievement['name'].' - Level '.$achievement['level'].'"></div>';
		echo "<div class='col-sm-3 thumbnail' style='padding:.5em;margin:.5em;background-color:#f2f2f2;border-radius:10px;'><p style='width:100%;display:block;'><b>".$request['username']."</b><BR>Achievement: ".$request['achievementid']."<BR>Evidence: <a href='".$request['evidence']."'>LINK</a></p></div>";
	}
	}
	echo "</div></div>";
}
?>

<?php
if ($userrow['userlevel'] > 2){
	echo "<div class='row'><div style='padding-top:3%;' class='col-sm-1 col-sm-offset-1' ><h3>Admin Tasks</h3></div>
			<div style='margin-top:3em;' class='col-sm-6' id='giveachievementinfo'></div>
	<div class='col-sm-2' style='margin-top:3em;background-color:#f2f2f2;border-radius:10px;' id='myadmin'>";
		$achieve = $mysqli->query("SELECT * FROM achievementList ORDER BY name ASC");
	$ach_length = $achieve->num_rows;

	$empl = $mysqli->query("SELECT * FROM users");
	$emplength = $empl->num_rows;

	echo '<form method="get" class="form-group"><label for="employee">Give:</label><select class="form-control" name="employee" id="employee">';
	for($x=0;$x<$emplength;$x++) {
		$empRow = $empl->fetch_array(MYSQLI_ASSOC);
		echo '<option value="', $empRow['id'], '">' . $empRow['onid'] . ' - ' . $empRow['lastname'] . ', ' . $empRow['firstname'] . ' - ' . $empRow['username'] . '</option>';
	}
	echo '</select>';
	
	echo '<label for="giveachievement">the achievement</label>';
    echo '<select class="form-control" name="giveachievement" id="giveachievement" onchange="loadgivelevels()"><option>Choose an Achievement</option>';	
	for($i=0;$i<$ach_length;$i++) {
		$achRow = $achieve->fetch_array(MYSQLI_ASSOC);
		echo '<option value="', $achRow['id'], '">', $achRow['name'], '</option>';
	}
	echo '</select><select class="form-control" name="givelevel" id="givelevel" onchange="loadgiveachievementinfo()">';
	echo '</select><br><br>';
	echo '<input class="form-control" type="submit" value="Submit" name="btn-give"></button></form>';
	echo "</div></div>";
}
?>

<script>
	function loadgiveachievementinfo(){
		giveachievement = $('#giveachievement').val();
		givelevel = $('#givelevel').val();
		$.ajax({
		type: 'GET',
		url: './ajax/loadachievementinfo.php',
		dataType: 'html',
		data: {	givelevel: givelevel,
				giveachievement: giveachievement},
		success: function(result)
			{
			$('#giveachievementinfo').html(result);
			}
		});
	}
</script>

<script>
	function loadrequestlevels(){
		requestachievement = $('#requestachievement').val();
		$.ajax({
		type: 'GET',
		url: './ajax/loadlevels.php',
		dataType: 'html',
		data: {	requestachievement: requestachievement},
		success: function(result)
			{
			$('#requestlevel').html(result);
			loadrequestachievementinfo();
			}
		});
	}
</script>

<script>
	function loadgivelevels(){
		giveachievement = $('#giveachievement').val();
		$.ajax({
		type: 'GET',
		url: './ajax/loadlevels.php',
		dataType: 'html',
		data: {	giveachievement: giveachievement},
		success: function(result)
			{
			$('#givelevel').html(result);
			loadgiveachievementinfo();
			}
		});
	}
</script>

</body>
</html>
