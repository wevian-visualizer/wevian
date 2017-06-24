<?php
	session_start();
	if(!isset($_SESSION["logged_in"]) && !isset($_COOKIE["id"]) && !isset($_COOKIE["unique_hash_id"])){
		include("connect.php");
		include("functions.php");
		if($stmt = mysqli_prepare($connect, "SELECT id,email,password FROM accounts WHERE email=?")){
			$email = htmlspecialchars($_POST["email"]);
			$password = htmlspecialchars($_POST["password"]);
			mysqli_stmt_bind_param($stmt, "s", $email);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $id,$email_result,$password_hashed);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_close($stmt);
			if(password_verify($password,$password_hashed)){
				if(isset($_POST["remember_me"]) && !empty($_POST["remember_me"]) && $_POST["remember_me"]=="remember_me"){
					$unique_hash_id = password_hash($id,PASSWORD_DEFAULT);
					if($stmt = mysqli_prepare($connect, "UPDATE accounts SET remember_me_hash=? WHERE id=?")){
						mysqli_stmt_bind_param($stmt, "si", $unique_hash_id,$id);
						mysqli_stmt_execute($stmt);
						mysqli_stmt_close($stmt);
						setcookie("unique_hash_id", $unique_hash_id, time()+60*60*24*365);
						$id_hashed = password_hash($id,PASSWORD_DEFAULT);
						setcookie("id", $id_hashed, time()+60*60*24*365);
					}
				}else{
					$_SESSION["logged_in"] = true;
					$_SESSION["uid"] = $id;
					$_SESSION["email"] = $email;
					logUpdate("User Login\Refreshed");
				}
			}
			mysqli_close($connect);
		}
	}
	header("Location: index.php");
?>
