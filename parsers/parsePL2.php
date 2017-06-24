<?PHP
	session_start();
	$file = fopen("../upload/".$_SESSION["uid"]."/".$_SESSION["fid"]."/".$_SESSION["filename"], "r");
	$array = array();
	while (($buffer = fgets($file)) !== false) {
		if (preg_match('/^[\\s]*[a-z|A-Z|0-9]+[\\s]+[\-|0-9|\.]+[\\s]+[\-|0-9]+[\\s]+[:][\\s]+[a-z|A-Z]+/',$buffer)) {
			$expBuff = preg_split('/\s+/', $buffer);
			if(preg_match('/[a-zA-Z0-9]+/',$expBuff[0])){
				$array[$expBuff[0]]["ll_Xcoord"] = $expBuff[1];
				$array[$expBuff[0]]["ll_Ycoord"] = $expBuff[2];
				$array[$expBuff[0]]["orientation"] = $expBuff[4];
				if($expBuff[5] != ""){
					$array[$expBuff[0]]["movetype"] = $expBuff[5];
				}
			}else{
				$array[$expBuff[1]]["ll_Xcoord"] = $expBuff[2];
				$array[$expBuff[1]]["ll_Ycoord"] = $expBuff[3];
				$array[$expBuff[1]]["orientation"] = $expBuff[5];
				if($expBuff[6] != ""){
					$array[$expBuff[1]]["movetype"] = $expBuff[6];
				}
			}
		}
	}
	$temp = $temp1 = array();
	foreach ($array as $key => $row) {
		$temp[$key] = $row['ll_Ycoord'];
		$temp1[$key] = $row['ll_Xcoord'];
	}
	array_multisort($temp, SORT_DESC, $temp1, SORT_ASC, $array);
	//echo "<pre>".json_encode($array, JSON_PRETTY_PRINT)."</pre>";
	fclose($file);
	echo json_encode($array);
?>