<?php
include_once 'header.php';

$sourcePath = $_FILES['file']['tmp_name'];
$targetPath = "profiles/".$_FILES['file']['name'];
move_uploaded_file($sourcePath, $targetPath);
?>

<script>
	$.ajax({
		url: "upload.php",
		type: "POST",
		data: new FormData(this),
		contentType: false,
		cache: false,
		success: function(data) {
			$('#loading').hide();
			$('#message').html(data);
		}
	});
</script>

<form id="uploadimage" action="" method="post" enctype="multipart/form-data">
<hr id="line">
<div id="selectImage">
<label>Select Your Image</label><br/>
<input type="file" name="file" id="file" required /><br>
<input type="submit" value="Upload" class="submit" />
</div>
</form>
