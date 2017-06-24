<?php
	session_start();
	if(isset($_SESSION["logged_in"])){
		include("functions.php");
		logUpdate("User Logout");
		session_destroy();
		if(isset($_COOKIE["id"]) || !empty($_COOKIE["id"]) || isset($_COOKIE["unique_hash_id"]) || !empty($_COOKIE["unique_hash_id"])){
			setcookie("id", "", time() - 3600);
			setcookie("unique_hash_id", "", time() - 3600);
		}
	}
	header("Location: index.php");
?>