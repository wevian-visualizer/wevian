<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Wevian Visualizer</title>
		<script src="js/jquery.js"></script>
		<script src="js/script.js"></script>
		<script src="js/dropzone.js"></script>
		<script src="js/html2canvas.js"></script>
		<script src="js/Chart.bundle.js"></script>
		<link rel="stylesheet" type="text/css" href="css/style.css"/>
		<link rel="stylesheet" type="text/css" href="css/dropzone.css"/>
		<link rel="stylesheet" type="text/css" href="css/modal.css"/>
		<script>
		Dropzone.options.dropzone = {
			paramName: "file",
			maxFilesize: 100,
			uploadMultiple: false,
			maxFiles: 1,
			acceptedFiles: ".pl",
			dictDefaultMessage: "1. Select type from List.<br>2. Drop (.pl) file or click here.",
			dictInvalidFileType: "Only '.pl' file is accepted.",
			success: function(file){
				setTimeout(function(){window.location.replace("index.php");},1000);
			}
		};
		</script>
	</head>
	<body>
<?PHP
	session_start();
	date_default_timezone_set('Europe/Athens');
	include("connect.php");
	include("functions.php");
	if(isset($_COOKIE["id"]) && !empty($_COOKIE["id"]) && isset($_COOKIE["unique_hash_id"]) && !empty($_COOKIE["unique_hash_id"])){
		if($stmt = mysqli_prepare($connect, "SELECT id,email,remember_me_hash FROM accounts WHERE remember_me_hash=?")){
			mysqli_stmt_bind_param($stmt, "s", $_COOKIE["unique_hash_id"]);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $id,$email,$remember_me_hash);
			mysqli_stmt_fetch($stmt);
			mysqli_stmt_close($stmt);
			if(password_verify($id,$_COOKIE["id"])){
				$_SESSION["logged_in"] = true;
				$_SESSION["uid"] = $id;
				$_SESSION["email"] = $email;
				logUpdate("User Login\Refreshed");
			}
		}
	}
?>
		<div class="divLeft">
<?PHP
	if(isset($_SESSION["logged_in"]) && !empty($_SESSION["logged_in"])){
		if(isset($_SESSION["dirr"]) && !empty($_SESSION["dirr"])){
			echo '<div id="fixedDIV" onclick="onClickShowNets(event);"></div>';
		}
	}else{
?>
			<div id="loginRegisterDIV">
				<div id="loginDIV">
					<form action="login.php" method="POST">
						<span>Email</span><br>
						<input type="text" name="email" required><br>
						<span>Password</span><br>
						<input type="password" name="password" required><br>
						<input type="checkbox" name="remember_me" value="remember_me"><span>&nbsp;Remember me</span><br>
						<center><button class="loginRegisterBtn" name="submit">Login</button></center>
					</form>
				</div>
				<span id="horLine"></span>
				<div id="registerDIV">
					<form action="register.php" method="POST">
						<span>Email</span><br>
						<input type="text" name="email" required><br>
						<span>Password</span><br>
						<input id="password" type="password" name="password" required><br>
						<center><button id="reg_submit" class="loginRegisterBtn" name="submit">Register</button></center>
					</form>
				</div>
			</div>
<?PHP
	}
	if(isset($_SESSION["logged_in"]) && !empty($_SESSION["logged_in"]) && !isset($_SESSION["dirr"])){
		if($stmt = mysqli_prepare($connect, "SELECT id,type,name,date FROM files WHERE userid=?")){
			mysqli_stmt_bind_param($stmt, "i", $_SESSION["uid"]);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $info["id"],$info["type"],$info["name"],$info["date"]);
?>
			<div id="savedProjectsSpan">These are your saved projects from your uploads <u>(you need to upload at least 1 first)</u>, click one to see it or upload another!<br>If you don't want them saved then just delete them.</div>
<?PHP
			while(mysqli_stmt_fetch($stmt)){
?>
			<div onclick="loadFile(<?PHP echo $info["id"]; ?>);" id="fid<?PHP echo $info["id"]; ?>" class="loadfilediv">
				File name: <b><span id='infoname<?PHP echo $info["id"]; ?>'><?PHP echo $info["name"]; ?></span></b><br>
				Type: <span id='infotype<?PHP echo $info["id"]; ?>'><?PHP echo $info["type"]; ?></span><br>
				Uploaded at <b><span id='infodate<?PHP echo $info["id"]; ?>'><?PHP echo $info["date"]; ?></span></b> Athens/Greece local time.
			</div>
			<div id="fid2_<?PHP echo $info["id"]; ?>" class="loadfilediv2">
				<span style="float: left; cursor: pointer;" onclick="loadFile(<?PHP echo $info["id"]; ?>);">Visualise</span>
				<a style="color: rgb(255,100,150); text-decoration: none;" href="upload/<?PHP echo $_SESSION["uid"]; ?>/<?PHP echo $info["id"]; ?>/<?PHP echo substr($info["name"],0,-3); ?>.txt" download="<?PHP echo $info["name"]; ?>" download>Download <?PHP echo $info["name"]; ?></a>
				<span onclick="deleteFile(<?PHP echo $info["id"]; ?>);" style="float: right; cursor: pointer;">Delete</span>
			</div>
<?PHP
			}
			mysqli_stmt_close($stmt);
		}
	}
?>
		</div>
<?PHP
	if(isset($_SESSION["logged_in"]) && !empty($_SESSION["logged_in"])){
		if(isset($_SESSION["dirr"]) && !empty($_SESSION["dirr"])){
			echo '<script>parseSCL_NODES_PL();</script>';
		}
	}
	if(isset($_SESSION["logged_in"]) && !empty($_SESSION["logged_in"])){
?>
		<div class="divCenter"><button class="triangleRight" onclick="divRightSlide();"></button></div>
		<div class="divRight">
			<div id="menu">
				<ul>
<?PHP
	if(isset($_SESSION["dirr"]) && !empty($_SESSION["dirr"])){
?>
					<li id="highlightCell">
						<input placeholder="Node name" id="cellInput" type="text">
						<button id="cellInputBtn" onclick="panelButtons(0);">Highlight Cell</button>
					</li>
					<li id="clearHighlightedCell">
						<input placeholder="Node name" id="cellClear" type="text">
						<button id="cellClearBtn" onclick="panelButtons(1);">Clear Cell</button>
					</li>
					<li id="displayNets">
						<input placeholder="Node name" id="NetInput" type="text">
						<button id="displayNetsBtn" onclick="panelButtons(4);">Highlight Net</button>
						<span id="executedTimeData3"></span>
					</li>
					<li id="clearNets">
						<input placeholder="Node name" id="NetClearInput" type="text">
						<button id="clearNetsBtn" onclick="panelButtons(6);">Clear Net</button>
						<hr>
					</li>
					<li id="displayRows">
						<span id="spanRows" style="font-size: 14px;">Show Rows</span>
						<div style="vertical-align:middle;" id="displayRowsBtn" onclick="$('.toggle-button').toggleClass('toggle-button-selected'); panelButtons(8);" class="toggle-button">
							<button id="displayRowsBtn2"></button>
						</div>
					</li>
					<li id="displayOverlap">
						<span id="spanOverlap" style="font-size: 14px;">Show Overlap</span>
						<div style="vertical-align:middle;" id="displayOverlapBtn" onclick="$('.toggle-button2').toggleClass('toggle-button-selected2'); panelButtons(3);" class="toggle-button2">
							<button id="displayOverlapBtn2"></button>
						</div>
						<span id="executedTimeData2"></span>
					</li>
					<li id="displayOverflow">
						<span id="spanOverflow" style="font-size: 14px;">Show Overflow</span>
						<div style="vertical-align:middle;" id="displayOverflowBtn" onclick="$('.toggle-button3').toggleClass('toggle-button-selected3'); panelButtons(9);" class="toggle-button3">
							<button id="displayOverflowBtn2"></button>
						</div>
						<span id="executedTimeData4"></span>
					</li>
					<li id="displayHeatmap">
						<span id="spanHeatmap" style="font-size: 14px;">Show Thermal Map</span>
						<div style="vertical-align:middle;" id="displayHeatmapBtn" onclick="$('.toggle-button4').toggleClass('toggle-button-selected4'); panelButtons(10);" class="toggle-button4">
							<button id="displayHeatmapBtn2"></button>
						</div>
						<span id="executedTimeData5"></span>
					</li>
					<li id="displayCongestion">
						<input placeholder="Box Num." id="BoxInput" type="text">
						<button id="displayCongestionBtn" onclick="panelButtons(11);">Congestion Map</button>
						<span id="executedTimeData6"></span>
					</li>
					<li id="clearCongestion">
						<button id="clearCongestionBtn" onclick="panelButtons(13);">Clear Congestion Map</button>
					</li>
					<li id="displayHalfPerimeter">
						<button id="displayHalfPerimeterBtn" onclick="panelButtons(12);">Calculate Half-Perimeter</button>
						<span id="executedTimeData7"></span>
						<hr>
					</li>
					<li id="cellInfoHeader">
						<table class="cellInfo">
							<tr>
								<th>Name</th>
								<th>X</th>
								<th>Y</th>
								<th>Orient.</th>
								<th>Movetype</th>
								<th>x</th>
							</tr>
						</table>
						<hr>
					</li>
					<li id="exportInfo">
						<button id="exportBtn" onclick="panelButtons(2);">Show statistics</button>
						<div id="statsModal" class="modal" style="z-index: 1000;">
							<div class="modal-content">
								<span class="close">&times;</span>
								<div id="modalContent"></div>
							</div>
						</div>
					</li>
					<li id="exportoIMAGE">
						<button id="exportIMAGE" onclick="panelButtons(14);">Export to PNG</button>
					</li>
					<li id="exportoPDF">
						<button id="exportPDF" onclick="panelButtons(7);">Print</button>
						<hr>
					</li>
					<li id="clearVisualizer">
						<button id="session_destroy" onclick="panelButtons(5);">Clear <?PHP echo $_SESSION["dirr"]; ?> visualizer</button>
					</li>
<?PHP
	}
	if(!isset($_SESSION["dirr"])){
?>
					<li id="formDropzone">
						<form action="upload.php" id="dropzone" method="POST" class="dropzone">
							<center>
								<select id="selectOptions" name="options" required>
									<option disabled selected value=""> -- Select an option -- </option>
									<option value="ibm01">ibm01</option>
									<option value="ibm02">ibm02</option>
									<option value="ibm03">ibm03</option>
									<option value="ibm04">ibm04</option>
									<option value="ibm05">ibm05</option>
									<option value="ibm06">ibm06</option>
									<option value="ibm07">ibm07</option>
									<option value="ibm08">ibm08</option>
									<option value="ibm09">ibm09</option>
									<option value="ibm10">ibm10</option>
									<option value="ibm11">ibm11</option>
									<option value="ibm12">ibm12</option>
									<option value="ibm13">ibm13</option>
									<option value="ibm14">ibm14</option>
									<option value="ibm15">ibm15</option>
									<option value="ibm16">ibm16</option>
									<option value="ibm17">ibm17</option>
									<option value="ibm18">ibm18</option>
									<option value="adaptec1">adaptec1</option>
									<option value="adaptec2">adaptec2</option>
									<option value="adaptec3">adaptec3</option>
									<option value="adaptec4">adaptec4</option>
									<option value="bigblue1">bigblue1</option>
									<option value="bigblue2">bigblue2</option>
									<option value="bigblue3">bigblue3</option>
									<option value="bigblue4">bigblue4</option>
									<option value="newblue1">newblue1</option>
									<option value="newblue2">newblue2</option>
									<option value="newblue3">newblue3</option>
									<option value="newblue4">newblue4</option>
									<option value="newblue5">newblue5</option>
									<option value="newblue6">newblue6</option>
									<option value="newblue7">newblue7</option>
								</select>
							</center>
						</form >
					</li>
<?PHP
	}
?>
					<li>
						<form action="logout.php" method="POST">
							<button name="submit">Logout (<?PHP echo $_SESSION["email"]; ?>)</button>
						</form>
					</li>
<?PHP
	if(isset($_SESSION["dirr"]) && !empty($_SESSION["dirr"])){
?>
					<li id="executedTime">
						<span id="executedTimeData"></span>
					</li>
<?PHP
	}
?>
				</ul>
			</div>
		</div>
<?PHP
	}
?>
	</body>
</html>
