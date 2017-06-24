<?PHP
	function logUpdate($reason){
		include("connect.php");
		$date = date("Y-m-d H:i:s");
		if($stmt = mysqli_prepare($connect, "INSERT INTO log (reason,user,ip,date) VALUES (?,?,?,?)")){
			mysqli_stmt_bind_param($stmt, "siss", $reason,$_SESSION["uid"],$_SERVER['REMOTE_ADDR'],$date);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
		}
	}
	
	function updateSession(){
		session_start();
		$_SESSION["dirr"] = $_POST["type"];
		$_SESSION["fid"] = $_POST["fid"];
		$_SESSION["filename"] = $_POST["name"];
		logUpdate("User Load ".$_SESSION["filename"]." (".$_POST["type"].")");
	}
	
	function clearVisualizer(){
		session_start();
		if(isset($_SESSION["logged_in"]) && !empty($_SESSION["logged_in"])){
			logUpdate("User Clear ".$_SESSION["filename"]." (".$_SESSION["dirr"].")");
			unset($_SESSION["dirr"]);
			unset($_SESSION["filename"]);
			unset($_SESSION["fid"]);
		}
	}
	
	function deleteFile(){
		session_start();
		include("connect.php");
		if($stmt = mysqli_prepare($connect, "DELETE FROM files WHERE userid=? and id=?")){
			mysqli_stmt_bind_param($stmt, "ii", $_SESSION["uid"],$_POST["fid"]);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			logUpdate("User Delete ".$_POST["fid"]);
		}
	}
	
	if(isset($_POST["loadFile"]) && $_POST["loadFile"]) updateSession();
	if(isset($_POST["clearVisualizer"]) && $_POST["clearVisualizer"]) clearVisualizer();
	if(isset($_POST["deleteFile"]) && $_POST["deleteFile"]) deleteFile();
?>