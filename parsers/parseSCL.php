<?PHP
	session_start();
	$file = fopen("../benchmarks/".$_SESSION["dirr"]."/".$_SESSION["dirr"].".scl", "r");
	while (($buffer = fgets($file)) !== false) {
		if (strpos($buffer, "NumRows") !== false) {
			$numRows = explode(':', $buffer);
			$array["NumRows"] = intval($numRows[1]);
			break;
		}
	}
	$buffer = fgets($file);
	while (($buffer = fgets($file)) !== false) {
		for ($j=0; $j<7; $j++) {
			$buffer = fgets($file);
			$expBuff = explode(":", $buffer);
			if ($j<4) {
				$element = intval($expBuff[1]);
				$array1[str_replace(' ', '', $expBuff[0])] = $element;
			} else if ($j<6) {
				$element = str_replace(' ', '', $expBuff[1]);
				$array1[str_replace(' ', '', $expBuff[0])] = $element;
			} else {
				$element = intval($expBuff[1]);
				$array1["SubrowOrigin"] = $element;
				$element = intval($expBuff[2]);
				$array1["Numsites"] = $element;
			}
		}
		$buffer = fgets($file);
		array_push($array, $array1);
	}
	//echo "<pre>".json_encode($array, JSON_PRETTY_PRINT)."</pre>";
	fclose($file);
	echo json_encode($array);
?>