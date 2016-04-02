<?php
include_once 'header.php';

if (isset($_POST['user_choice'])) {
	$avatar = $_POST['user_choice'];
	$id = $userrow['id'];
	
	$file_ext = strtolower(end(explode('.', $avatar)));
	$file_name = $userrow['hash'] . '.' . $file_ext;
	
	$query = "UPDATE users SET avatar='$file_name' WHERE id='$id'";
	
	$isdefault = 0;
	$imgRes = $mysqli->query("SELECT * FROM defaultImage ORDER BY id DESC");
 	
	for($i=0;$i<$imgRes->num_rows;$i++) {
		$imgRow = $imgRes->fetch_array(MYSQLI_ASSOC);
		if ($imgRow['url'] == $userrow['avatar']) {
			$isdefault = 1;
		}
	}
 	
	if($isdefault == 0) {
		if (file_exists("avatars/".$userrow['avatar'])) {
			unlink("avatars/".$userrow['avatar']);
		}
	}
  
	copy("avatars/".$avatar, "avatars/".$file_name);
	
	$mysqli->query($query);
	
	header("Location: profile.php");
}

if (isset($_POST['btn-update'])) {
	$name = $_POST['username'];
	$id = $userrow['id'];
	$query = "UPDATE users SET username='$name' WHERE id='$id'";	
	$mysqli->query($query);
	header("Location: profile.php");
}

if (isset($_FILES['image'])) {
	$id = $userrow['id'];
	
	$errors = array();
	$file_name = $_FILES['image']['name'];
	$file_size = $_FILES['image']['size'];
	$file_tmp = $_FILES['image']['tmp_name'];
	$file_type = $_FILES['image']['type'];
	$file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));

	$file_name = $userrow['hash'] . '.' . $file_ext;

	$extensions = array("jpeg","jpg","png");

	if (in_array($file_ext, $extensions) === false) {
		$errors[] = "Please choose a JPEG or PNG file.";
		?> <script>alert('Please choose a JPEG or PNG file.');</script><?php
	}

	if ($file_size > 2097152) {
		$errors[] = "File size must be < 2MB";
		?> <script>alert('File size must be < 2MB');</script> <?php
	}

	if (empty($errors) == true) {
		$isdefault = 0;
		$imgRes = $mysqli->query("SELECT * FROM defaultImage ORDER BY id DESC");
 	
		for($i=0;$i<$imgRes->num_rows;$i++) {
			$imgRow = $imgRes->fetch_array(MYSQLI_ASSOC);
			if ($imgRow['url'] == $userrow['avatar']) {
				$isdefault = 1;
			}		
		}
 	
		if($isdefault == 0) {
			if (file_exists("avatars/".$userrow['avatar'])) {
				echo '<h2><br><br><br>unlink</h2>';
				unlink("avatars/".$userrow['avatar']);
			}
		}
		
		move_uploaded_file($file_tmp, "avatars/".$file_name);
		$query = "UPDATE users SET avatar='$file_name' WHERE id='$id'";
		$mysqli->query($query);
		header("Location: profile.php");
	} else {
	}
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

<br><br><br><br>

<div class="panel">
<div class="container">
<div class="row">
<div class="col-lg-5">
<div class="media">
<a class="pull-left" href="#">
<?php
	$usrimg = $userrow['avatar'];
	$firstname = $userrow['firstname'];
	$lastname = $userrow['lastname'];
	$username = $userrow['username'];
	if ($userrow['userlevel'] == 3) {
		$level = "Admin";
	} else if ($userrow['userlevel'] == 2) {
		$level = "Reviewer";
	} else if ($userrow['userlevel'] == 1) {
		$level = "Standard";
	} else if ($userrow['userlevel'] == 0) {
		$level = "New";
	}

	echo '<img class="media-object dp img-circle" src="avatars/', $usrimg, '" style="width: auto;height:150px;">
		</a>
		<div class="media-body"><br>
		<h4 class="media-heading">', $firstname, ' ', $lastname, ' <small> ', $username, '</small></h4>
		<h5>User Level: ', $level, '</h5>
		</div>
		</div>
		</div>
		</div>
		</div><br>
		</div>';
?>

<div class="container"><div class="row"><div class="col-lg-12">
<h2>Choose a default profile picture:</h2>

<form action="profile.php" method="post" />
<?php
$query = "SELECT * FROM defaultImage ORDER BY id DESC";
$defRes = $mysqli->query($query);

for ($i=0; $i<$defRes->num_rows; $i++) {
	$defRow = $defRes->fetch_array(MYSQLI_ASSOC);
	$url = $defRow['url'];
	echo '<button name="user_choice" value="', $url, '" width="90" height="90"><img src="avatars/', $url, '" width="90" height="90"/></button>';
}
?>

</form>

</div>
</div>
</div>
<br>
<div class="container"><div class="row"><div class="col-lg-12">
<h2>Or, Upload a custom profile picture:</h2>

<div class="form-group">
<form action="" method="post" enctype="multipart/form-data">
<label>Select a JPG, PNG, or JPEG image under 2MB</label><br/>
<input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
<input type="file" name="image" required /><br>
<input type="submit" value="Upload Image" class="submit" />
</form>
</div>

</div>
</div>
</div>

<br><br>

<div class="container"><div class="row"><div class="col-lg-12">
<h2>Update your username:</h2>
<form id="updateusername" action="profile.php" method="post">
<textarea class="form-control" name="username" required>
<?php echo $userrow['username'] ?>
</textarea><BR>
<input type="submit" name="btn-update"></button>
</form></div></div></div><br><br>
