<?php
	session_start();
	if(!isset($_SESSION["logged_in"]) && !isset($_COOKIE["id"]) && !isset($_COOKIE["unique_hash_id"])){
		include("connect.php");
		include("functions.php");
		if($stmt = mysqli_prepare($connect, "INSERT INTO accounts (email,password) VALUES (?,?)")){
			$email = htmlspecialchars($_POST["email"]);
			$password = htmlspecialchars($_POST["password"]);
			$password = password_hash($password,PASSWORD_DEFAULT);
			mysqli_stmt_bind_param($stmt, "ss", $email,$password);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			logUpdate("User Register (".$email.")");
		}
		mysqli_close($connect);
	}
	header("Location: index.php");
?>