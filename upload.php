<?php
	session_start();
	include("connect.php");
	include("functions.php");
	date_default_timezone_set('Europe/Athens');
	if(isset($_SESSION["logged_in"]) && !empty($_SESSION["logged_in"])){
		if(isset($_POST["options"]) && !empty($_POST["options"])){
			$_SESSION["dirr"] = $_POST["options"];
			if($stmt = mysqli_prepare($connect, "SELECT id FROM accounts WHERE email=?")){
				$email = $_SESSION["email"];
				mysqli_stmt_bind_param($stmt, "s", $email);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_bind_result($stmt, $id);
				mysqli_stmt_fetch($stmt);
				mysqli_stmt_close($stmt);
				$options = $_POST["options"];
				$date = date("Y-m-d H:i:s");
				$filename = $_FILES["file"]["name"];
				$_SESSION["filename"] = $filename;
				$selected = 0;
				if($stmt = mysqli_prepare($connect, "INSERT INTO files (type,name,userid,date,selected) VALUES (?,?,?,?,?)")){
					mysqli_stmt_bind_param($stmt, "ssisi", $options,$filename,$id,$date,$selected);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_close($stmt);
					if($stmt = mysqli_prepare($connect, "SELECT id FROM files WHERE userid=? and type=? and name=? and date=? and selected=?")){
						mysqli_stmt_bind_param($stmt, "isssi", $id,$options,$filename,$date,$selected);
						mysqli_stmt_execute($stmt);
						mysqli_stmt_bind_result($stmt, $fid);
						mysqli_stmt_fetch($stmt);
						mysqli_stmt_close($stmt);
						$_SESSION["fid"] = $fid;
						if($stmt = mysqli_prepare($connect, "UPDATE files SET selected=1 WHERE userid=? and type=? and name=? and date=? and selected=?")){
							mysqli_stmt_bind_param($stmt, "isssi", $id,$options,$filename,$date,$selected);
							mysqli_stmt_execute($stmt);
							mysqli_stmt_close($stmt);
							$_SESSION["uid"] = $id;
							if(!file_exists("upload")){
								mkdir("upload");
							}
							if(!file_exists($id)){
								mkdir("upload/".$id);
							}
							if(!file_exists($id."/".$fid)){
								mkdir("upload/".$id."/".$fid);
							}
							if(!move_uploaded_file($_FILES["file"]["tmp_name"], "upload/".$id."/".$fid."/".basename($_FILES["file"]["name"]))){
								die("Sorry, there was an error uploading your file.");
							}
							copy("upload/".$id."/".$fid."/".basename($_FILES["file"]["name"]), substr("upload/".$id."/".$fid."/".basename($_FILES["file"]["name"]),0,-3).".txt");
							logUpdate("User Upload ".$_FILES["file"]["name"]." (".$options.")");
						}
					}
				}
			}
		}else{
			header("Location: index.php");
		}
	}else{
		header("Location: index.php");
	}
?>