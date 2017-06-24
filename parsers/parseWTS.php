<?PHP
	session_start();
	$file = fopen("../benchmarks/".$_SESSION["dirr"]."/".$_SESSION["dirr"].".wts", "r");
	$array = array();
	$max = -1;
	$min = 9999;
	while (($buffer = fgets($file)) !== false) {
		if (preg_match('/^[\\s]*[a-z|A-Z][a-z|A-Z|0-9]+[\\s]+[0-9]+/',$buffer)) {
			$expBuff = preg_split('/\s+/', $buffer);
			$array1["node_name"] = $expBuff[1];
			$array1["value"] = $expBuff[2];
			if($array1["value"]>$max){
				$max = intval($array1["value"]);
			}
			if($array1["value"]<$min){
				$min = intval($array1["value"]);
			}
			array_push($array, $array1);
		}
	}
	for($i=0;$i<sizeof($array);$i++){
		$array[$i]["value"] = number_format(intval($array[$i]["value"])/intval($max), 2, '.', '');
	}
	$array["min"] = intval($min);
	$array["max"] = intval($max);
	//echo "<pre>".json_encode($array, JSON_PRETTY_PRINT)."</pre>";
	fclose($file);
	echo json_encode($array);
?>