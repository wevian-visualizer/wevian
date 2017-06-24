<?PHP
	session_start();
	$file = fopen("../benchmarks/".$_SESSION["dirr"]."/".$_SESSION["dirr"].".nodes", "r");
	while (($buffer = fgets($file)) !== false) {
		if (strpos($buffer, "NumNodes") !== false) {
			$NumNodes = explode(':', $buffer);
			$array["NumNodes"] = intval($NumNodes[1]);
			continue;
		}
		if(strpos($buffer, "NumTerminals") !== false){
			$NumTerminals = explode(':', $buffer);
			$array["NumTerminals"] = intval($NumTerminals[1]);
			break;
		}
	}
	while (($buffer = fgets($file)) !== false) {
			$expBuff = preg_split('/\s+/', $buffer);
			$array1["node_name"] = $expBuff[1];
			$array1["width"] = $expBuff[2];
			$array1["height"] = $expBuff[3];
			if($expBuff[4] != ""){
				$array1["movetype"] = $expBuff[4];
			}
			array_push($array, $array1);
	}
	//echo "<pre>".json_encode($array, JSON_PRETTY_PRINT)."</pre>";
	fclose($file);
	echo json_encode($array);
?>