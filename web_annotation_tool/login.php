<?php
	include 'inc/users.php';
	if (isset($_SESSION['logged_in'])){
		session_destroy();
	}
	if (isset($_POST['user']) && isset($_POST['password'])) {
		//Extremely insecure... but is a stop gap measure
		if($USER[$_POST['user']] == $_POST['password']) {
			$_SESSION['user'] = $USER;
			$_SESSION['logged_in'] = true;
			header("Location: index.php");
		}
	}
?>

<html>
<h1>Image Tagging Framework</h1>
<form method="post">
	Username: <input type="text" name="user"/><br/>
	Password: <input type="password" name="password"/>
</form>
</html>