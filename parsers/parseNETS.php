<?PHP
	session_start();
	$file = fopen("../benchmarks/".$_SESSION["dirr"]."/".$_SESSION["dirr"].".nets", "r");
	while (($buffer = fgets($file)) !== false) {
		if (strpos($buffer, "NumNets") !== false) {
			$NumNets = explode(':', $buffer);
			$array["NumNets"] = intval($NumNets[1]);
			continue;
		}
		if(strpos($buffer, "NumPins") !== false){
			$NumPins = explode(':', $buffer);
			$array["NumPins"] = intval($NumPins[1]);
			break;
		}
	}
	while (($buffer = fgets($file)) !== false) {
		if(strpos($buffer, "NetDegree") !== false){
			$NetDegree = explode(':', $buffer);
			$array1["NetDegree"] = intval($NetDegree[1]);
		}
		for($i=0;$i<$array1["NetDegree"];$i++){
			if(($buffer = fgets($file)) !== false){
				$expBuff = preg_split('/\s+/', $buffer);
				$array1["node_name".$i] = $expBuff[1];
				$array1["pin_direction".$i] = $expBuff[2];
				if(isset($expBuff[4])){
						$array1["Xoffset".$i] = $expBuff[4];
				}
				if(isset($expBuff[5])){
						$array1["Yoffset".$i] = $expBuff[5];
				}
				
			}
		}
		array_push($array, $array1);
		unset($array1);
	}
	//echo "<pre>".json_encode($array, JSON_PRETTY_PRINT)."</pre>";
	fclose($file);
	echo json_encode($array);
?>