<?php
include_once 'header.php';

echo '<body>';

if ($userrow['userlevel'] > 1) {
	echo '<div style="position:fixed;margin-top:5em;background-color:#f2f2f2;border-radius:center:10px;" class="col-sm-2">
		<h2>Clocking</h2>
		<form class="form-group">
		<p>I am in room<p> 
		<select id="room" class="form-control">
		<option value="1">KEC 1119</option>
		</select>
		<input type="hidden" id="id" value=', $userrow['id'], '><br><button onclick="setEmpStatus()">Update</button>
		<button onclick="empLogout()">Logout</button></form></div>';	
}

if (isset($_REQUEST['btn-signup'])) {
	$firstname = trim(mysqli_real_escape_string($mysqli, $_REQUEST['firstname']));
	$lastname = trim(mysqli_real_escape_string($mysqli, $_REQUEST['lastname']));
	$username = trim(mysqli_real_escape_string($mysqli, $_REQUEST['username']));
	$onid = phpCAS::getUser();
	
	$data = array();
	$data['firstname'] = $firstname;
	$data['lastname'] = $lastname;
	$data['username'] = $username;
	$data['onid'] = $onid;
	
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
	
	if(isset($_REQUEST['username'])) {
		$pattern['a'] = '/[a]/'; $replace['a'] = '[a A @]';
		$pattern['b'] = '/[b]/'; $replace['b'] = '[b B I3 l3 i3]';
		$pattern['c'] = '/[c]/'; $replace['c'] = '(?:[c C (]|[k K])';
		$pattern['d'] = '/[d]/'; $replace['d'] = '[d D]';
		$pattern['e'] = '/[e]/'; $replace['e'] = '[e E 3]';
		$pattern['f'] = '/[f]/'; $replace['f'] = '(?:[f F]|[ph pH Ph PH])';
		$pattern['g'] = '/[g]/'; $replace['g'] = '[g G 6]';
		$pattern['h'] = '/[h]/'; $replace['h'] = '[h H]';
		$pattern['i'] = '/[i]/'; $replace['i'] = '[i I l ! 1]';
		$pattern['j'] = '/[j]/'; $replace['j'] = '[j J]';
		$pattern['k'] = '/[k]/'; $replace['k'] = '(?:[c C (]|[k K])';
		$pattern['l'] = '/[l]/'; $replace['l'] = '[l L 1 ! i]';
		$pattern['m'] = '/[m]/'; $replace['m'] = '[m M]';
		$pattern['n'] = '/[n]/'; $replace['n'] = '[n N]';
		$pattern['o'] = '/[o]/'; $replace['o'] = '[o O 0]';
		$pattern['p'] = '/[p]/'; $replace['p'] = '[p P]';
		$pattern['q'] = '/[q]/'; $replace['q'] = '[q Q 9]';
		$pattern['r'] = '/[r]/'; $replace['r'] = '[r R]';
		$pattern['s'] = '/[s]/'; $replace['s'] = '[s S $ 5]';
		$pattern['t'] = '/[t]/'; $replace['t'] = '[t T 7]';
		$pattern['u'] = '/[u]/'; $replace['u'] = '[u U v V]';
		$pattern['v'] = '/[v]/'; $replace['v'] = '[v V u U]';
		$pattern['w'] = '/[w]/'; $replace['w'] = '[w W vv VV]';
		$pattern['x'] = '/[x]/'; $replace['x'] = '[x X]';
		$pattern['y'] = '/[y]/'; $replace['y'] = '[y Y]';
		$pattern['z'] = '/[z]/'; $replace['z'] = '[z Z 2]';
		$word = str_split(strtolower($_REQUEST['username']));
		$i=0;
		while($i < count($word)) {
			if(!is_numeric($word[$i])) {
				if($word[$i] != ' ' || count($word[$i]) < '1') {
					$word[$i] = preg_replace($pattern[$word[$i]], $replace[$word[$i]], $word[$i]);
				}
			}
			$i++;
		}
		if(is_profanity($word) == 1) {
			email_message('Username Review Request', 'heer@oregonstate.edu', create_message('./emails/profanity.eml', $data));
		}
	}
}


if(isset($_REQUEST['btn-request'])) {
	$achievement = mysqli_real_escape_string($mysqli, $_REQUEST['requestachievement']);
	$evidence = mysqli_real_escape_string($mysqli, $_REQUEST['evidence']);
	$level = mysqli_real_escape_string($mysqli, $_REQUEST['requestlevel']);
	$userid = $userrow['id'];
	
	$empRes = $mysqli->query("SELECT achievements.*, levels.level FROM achievements INNER JOIN levels ON levels.id = achievements.levelid WHERE userid=$userid");
	$userAch = Array();
	while ($empRow = $empRes->fetch_array(MYSQLI_ASSOC))
		$userAch[] = $empRow;
	
	$levelRes = $mysqli->query("SELECT * FROM levels WHERE achievementid='$achievement' && level='$level'");
	$levelRow = $levelRes->fetch_array(MYSQLI_ASSOC);
	
	$done = false;
	foreach($userAch AS $row) {
		if($row['levelid'] == $levelRow['id']) {
			echo "<script>alert('You already have this achievement');</script>";
			$done = true;
		}
	}
	
	if ($done == false){
		$reqAch = $levelRow['id'];
		$query = "SELECT * FROM achievementList WHERE id = $achievement";
		$result = $mysqli->query($query);
		$row = $result->fetch_assoc();
		$userrow['name'] = $row['name'];
		$userrow['level'] = $level;
		
		$query = "SELECT requests.*, levels.level FROM requests INNER JOIN levels ON levels.id = requests.achievementid WHERE requests.requesterid = '$userid' AND requests.achievementid = '$reqAch' AND requests.status = 0";
		$result = $mysqli->query($query);
		if ($result->num_rows > 0) {//Already Under Review
			$row = $result->fetch_assoc();
			echo "<script>alert('You already have an open request to be reviewed for level " . $row['level'] . " of the " . $userrow['name'] . " achievement. Please wait for the that review to complete.');</script>";
		} else {
			$userrow['hash'] = randomhash();
			$mysqli->query("INSERT INTO requests(requesterid, achievementid, hash, evidence) VALUES('$userid', '$reqAch', '".$userrow['hash']."', '$evidence')");
			$requestid = $mysqli->insert_id;
			
			//Email the requester with information
			$query = "SELECT * FROM achievementList WHERE id = $achievement";
			$result = $mysqli->query($query);
			$row = $result->fetch_assoc();
			$userrow['name'] = $row['name'];
			$userrow['level'] = $level;
			email_message('Achievement Request', $userrow['onid'] . '@oregonstate.edu', create_message('./emails/request.eml', $userrow));
			
			//Identify reviewers
			$query = "SELECT DISTINCT users.* FROM users INNER JOIN achievements ON achievements.userid = users.id INNER JOIN levels ON levels.id = achievements.levelid WHERE achievements.achievementid = $achievement AND levels.level >= $level GROUP BY users.id LIMIT 5";
			//echo $query . '<BR>';
			$result = $mysqli->query($query);
			while($row = $result->fetch_assoc()){
				//echo 'Emailing: ' . $row['username'] . '<BR>';
				$userrow['reviewername'] = $row['username'];
				if (email_message('Achievement Review Request', $row['onid'] . '@oregonstate.edu', create_message('./emails/committee.eml', $userrow)) == 0){ // It worked
					$query = "INSERT INTO reviews (requestid, reviewer, emaileddate) VALUES ($requestid, " . $row['id'] . ", NOW())";
					$mysqli->query($query);
				}
				
			}
			
		}
	}
}

if(isset($_REQUEST['btn-give'])) {
	$achievement = mysqli_real_escape_string($mysqli, $_REQUEST['giveachievement']);
	$level = mysqli_real_escape_string($mysqli, $_REQUEST['givelevel']);
	$employeeid = mysqli_real_escape_string($mysqli, $_REQUEST['employee']);	
	
	addachievement($mysqli, $achievement, $level, $employeeid);
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

?>

<nav class="navbar navbar-inverse navbar-fixed-top">
		<a class="navbar-brand" href="http://www.oregonstate.edu">Oregon State University</a>
		<div style="padding-right:1%;">
<?php
if (isset($onid)){
	echo '<div class="navbar-brand pull-right" style="padding-right:1%;"><span class="glyphicon glyphicon-user"></span> <a href="profile.php">Account (' . $onid . ')</a> - <a href="' . $_SERVER['PHP_SELF'] . '?logout">Logout</a> - <a href="home.php">Home</a></div>';
} else {
	echo '<a href="' . $_SERVER['PHP_SELF'] . '?login"><button type="button" class="btn btn-default navbar-btn pull-right">Sign in</button></a>';
}
?>
		</div>
</nav>

<?php

if (isset($onid)){
	$x = $mysqli->query("SELECT * FROM users WHERE onid='$onid'");
	$y = $x->num_rows;
	if($y == 0) {
		echo "<div class='row'><div style='padding-top:5em;' class='col-sm-8 col-sm-offset-2'><p>Welcome! We need you to register for the first time.</p>
		<center>
		<div id='login-form'>
		<form method='post'>
		<table align='center' width='30%' border='0'>
		<tr>
		<td><input type='text' name='firstname' placeholder='First Name' required /></td>
		</tr>
		<tr>
		<td><input type='text' name='lastname' placeholder='Last Name' required /></td>
		</tr>
		<tr>
		<td><input type='text' name='username' placeholder='Username' required /></td>
		</tr>
		<tr>
		<td><button type='submit' name='btn-signup'>Register</button></td>
		</tr>
		</table>
		</form>
		</div>
		<p><a href='?logout='>Logout</a></p>
		</center></div></div>";
		
		echo '</body>
</html>';
exit();
	} 
}
?>	
	
<div class='row'><div style="padding-top:5em;" class='col-sm-8 col-sm-offset-2'><h3>Your Achievements</h3></div></div>
<div class='row'><div class='col-sm-6 col-sm-offset-2' id='myachievements'>
<?php
$myachievements = achievementlist($mysqli, $onid);
if (empty($myachievements)){
	echo '<div class="col-sm-4"><h3>No Achievements Found.</h3></div>';
} else{
	foreach ($myachievements AS $achievement){
		echo '<div class="col-sm-2 thumbnail" style="border-style:none;"><img class="img-responsive" style="width:100%;display:block;" src="./img/'.$achievement['image'].'" title="'.$achievement['name'].' - Level '.$achievement['level'].'"></div>';
	}
}
$myachievements = mycurrentrequestslist($mysqli, $onid);
if (!empty($myachievements)){
	foreach ($myachievements AS $achievement){
		echo '<div class="col-sm-2 thumbnail" style="border-style:none;"><img class="img-responsive" style="width:100%;display:block;opacity: 0.4;
    filter: alpha(opacity=40);" src="./img/'.$achievement['image'].'" title="IN PROCESS: '.$achievement['name'].' - Level '.$achievement['level'].'"></div>';
	}
}
?>
</div><div style="margin-top:5em;background-color:#f2f2f2;border-radius:10px;" class='col-sm-2'>
<form method="post" class="form-group"><label for="requestachievement">Request achievement:</label>
<select required name="requestachievement" id="requestachievement" class="form-control" onchange="loadrequestlevels()"><option value="">Choose ...</option>
<?php
	$achieve = $mysqli->query("SELECT * FROM achievementList ORDER BY name");
	$ach_length = $achieve->num_rows;

	$empl = $mysqli->query("SELECT * FROM users");
	$emplength = $empl->num_rows;

	for($i=0;$i<$ach_length;$i++) {
		$achRow = $achieve->fetch_array(MYSQLI_ASSOC);
		echo '<option value="', $achRow['id'], '">', $achRow['name'], '</option>';
	}
?>
</select><select required name="requestlevel" id="requestlevel" class="form-control" onchange="loadrequestachievementinfo()"></select>
<label for="evidence">Link to Evidence:</label>
<input type="url" required name="evidence" id="evidence" class="form-control"><br><br>
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
	echo "<div class='row'><div style='padding-top:3%;' class='col-sm-8 col-sm-offset-2'><h3>Requests to Review</h3></div></div>";
	echo "<div class='row'><div class='col-sm-8 col-sm-offset-2' id='myreviews'>";
	$requests = requestslist($mysqli, $onid);
	if (empty($requests)){
		echo '<h2>You do not have any open requests to review</h2>';
	} else {
	foreach($requests as $request){
		echo "<div class='col-sm-3 thumbnail' style='padding:.5em;margin:.5em;background-color:#f2f2f2;border-radius:10px;'><p style='width:100%;display:block;'><b>".$request['username']."</b><BR>Achievement: ".$request['achievementid']."<BR><a href='./review.php?reviewhash=" . $request['hash'] . "'>Review</a></p></div>";
		}
	}
	echo "</div></div>";
}
?>

<?php
if ($userrow['userlevel'] > 2){
	echo "<div class='row'><div style='padding-top:3em;' class='col-sm-8 col-sm-offset-2' ><h3>Admin Tasks</h3></div></div>";
	echo "<div class='row'><div style='padding-top:3em;' class='col-sm-8 col-sm-offset-2' ><h4><a href='./admin/userlist.php'>User List</a> --- <a href='./admin/messageusers.php'>Message Users</a> --- <a href='./admin/editleveltext.php'>Edit Achievement Levels</a></h4></div></div>";
	echo "<div class='row'><div style='padding-top:3em;' class='col-sm-6 col-sm-offset-2' >";
	
	$approveids = Array();
	$denyids = Array();
	$abstainids = Array();
	
	$query = "SELECT id FROM verdicts WHERE verdict = 'Approve'";
	$result = $mysqli->query($query);
	while ($row = $result->fetch_assoc())
		$approveids[] = $row['id'];
	$query = "SELECT id FROM verdicts WHERE verdict = 'Abstain'";
	$result = $mysqli->query($query);
	while ($row = $result->fetch_assoc())
		$abstainids[] = $row['id'];
	$query = "SELECT id FROM verdicts WHERE verdict = 'Deny'";
	$result = $mysqli->query($query);
	while ($row = $result->fetch_assoc())
		$denyids[] = $row['id'];
	
	$approvals = approvalslist($mysqli);
	foreach ($approvals as $row){
		$query = "SELECT * FROM reviews WHERE requestid = " . $row['id'];
		$result = $mysqli->query($query);
		$votetext = '';
		$voteapproval = 0;
		$votedeny = 0;
		$voteabstain = 0;
		$votetotal = 0;
		while ($voterow = $result->fetch_assoc()){
			if (in_array($voterow['verdict'], $approveids))
				$voteapproval++;
			if (in_array($voterow['verdict'], $abstainids))
				$voteabstain++;
			if (in_array($voterow['verdict'], $denyids))
				$votedeny++;
			$votetotal++;
		}
		$query = "SELECT levels.level, levels.image, achievementList.name FROM levels INNER JOIN achievementList ON levels.achievementid = achievementList.id WHERE levels.id = " . $row['achievementid'] ." ORDER BY achievementList.name ASC, levels.level ASC";
		//echo $query . '<BR>';
		$result = $mysqli->query($query);
		$row2 = $result->fetch_assoc();
		$achv_text = $row2['name'] . ' - Lvl ' . $row2['level'];
		echo "<div class='col-sm-4 thumbnail' style='padding:.5em;margin:.5em;background-color:#f2f2f2;border-radius:10px;'>
		<div style='width:60%;float:left;'><p style='width:100%;display:block;'><b>" . date("m-d-Y",strtotime($row['created'])) . "</b><BR><b>".$row['username']."</b><BR>Achievement:<BR>".$achv_text."<BR>
		Evidence: <a href='".$row['evidence']."'>LINK</a><BR>A / B / D / T: $voteapproval / $voteabstain / $votedeny / $votetotal</p></div>
		<div style='width:30%;float:right;'>
		<form action='./approval.php' method='get'><input type='hidden' name='id' value='" . $row['id'] . "'><input class='form-control' type='submit' value='Approve' name='btn-approve'></button></form><BR><BR>
		<form action='./approval.php' method='get'><input type='hidden' name='reviewhash' value='" . $row['hash'] . "'><input class='form-control' type='submit' value='More...'></button></form><BR><BR>
		</div></div>";
		
	}
	
	echo "</div>";
	echo "
	<div class='col-sm-2' style='background-color:#f2f2f2;border-radius:10px;' id='myadmin'>";
		$achieve = $mysqli->query("SELECT * FROM achievementList ORDER BY name ASC");
	$ach_length = $achieve->num_rows;

	$empl = $mysqli->query("SELECT * FROM users ORDER BY onid ASC");
	$emplength = $empl->num_rows;

	echo '<form method="post" class="form-group"><label for="employee">Give:</label><select class="form-control" name="employee" id="employee"><option>Select User...</option>';
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
	
	
	echo "<div class='row'><div class='col-sm-offset-2 col-sm-6' id='giveachievementinfo'></div></div>";
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

<script>
	function setEmpStatus() {
		room = $('#room').val();
		id = $('#id').val();
		$.ajax({
			type: 'GET',
			url: './ajax/loadlevels.php',
			dataType: 'html',
			data: { room: room,
		   			id: id	},
			success: function(result) {
				$('#empstatus').html(result);
			}
		});
	}
</script>

<script>
	function empLogout() {
		id = $('#id').val();
		$.ajax({
			type: 'GET',
			url: './ajax/loadlevels.php',
			dataType: 'html',
			data: {id: id },
			success: function(result) {
				$('#empstatus').html(result);
			}
		});
	}
</script>

</body>
</html>
