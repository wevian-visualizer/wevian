<?PHP
	session_start();
	$file = fopen("../upload/".$_SESSION["uid"]."/".$_SESSION["fid"]."/".$_SESSION["filename"], "r");
	$array = array();
	while (($buffer = fgets($file)) !== false) {
		if (preg_match('/^[\\s]*[a-z|A-Z][a-z|A-Z|0-9]+[\\s]+[\-|0-9|\.]+[\\s]+[\-|0-9]+[\\s]+[:][\\s]+[a-z|A-Z]+/',$buffer)) {
			$expBuff = preg_split('/\s+/', $buffer);
			if(preg_match('/[a-zA-Z0-9]+/',$expBuff[0])){
				$array1["node_name"] = $expBuff[0];
				$array1["ll_Xcoord"] = $expBuff[1];
				$array1["ll_Ycoord"] = $expBuff[2];
				$array1["orientation"] = $expBuff[4];
				if($expBuff[5] != ""){
					$array1["movetype"] = $expBuff[5];
				}
			}else{
				$array1["node_name"] = $expBuff[1];
				$array1["ll_Xcoord"] = $expBuff[2];
				$array1["ll_Ycoord"] = $expBuff[3];
				$array1["orientation"] = $expBuff[5];
				if($expBuff[6] != ""){
					$array1["movetype"] = $expBuff[6];
				}
			}
			array_push($array, $array1);
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