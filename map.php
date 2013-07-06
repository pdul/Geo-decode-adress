<?php
		
	function gcode($address,$postcode,$city) {	
		
		$address = str_replace(" ", "+", $address);
		$city = str_replace(" ", "+", $city);

		$address = 'http://maps.googleapis.com/maps/api/geocode/json?address='.$address.'+'.$postcode.'+'.$city.'&sensor=true';
		
		$jsondata=file_get_contents($address);
		$output= json_decode($jsondata);
						
			if($output->status == 'OK') {
		 $address_latitude = $output->results[0]->geometry->location->lat;
		 $address_longitude = $output->results[0]->geometry->location->lng;
                } else {
				return array($output->status, $output->status);
				}
                return array($address_latitude, $address_longitude);
		}
		
	function csvstring_to_array($string, $separatorChar = ';', $enclosureChar = '', $newlineChar = "\r") {
		
		$array = array();
		$size = strlen($string);
		$columnIndex = 0;
		$rowIndex = 0;
		$fieldValue="";
		$isEnclosured = false;
		for($i=0; $i<$size;$i++) {

			$char = $string{$i};
			$addChar = "";

			if($isEnclosured) {
				if($char==$enclosureChar) {

					if($i+1<$size && $string{$i+1}==$enclosureChar){
						// escaped char
						$addChar=$char;
						$i++; // dont check next char
					}else{
						$isEnclosured = false;
					}
				}else {
					$addChar=$char;
				}
			}else {
				if($char==$enclosureChar) {
					$isEnclosured = true;
				}else {

					if($char==$separatorChar) {

						$array[$rowIndex][$columnIndex] = $fieldValue;
						$fieldValue="";

						$columnIndex++;
					}elseif($char==$newlineChar) {
						echo $char;
						$array[$rowIndex][$columnIndex] = $fieldValue;
						$fieldValue="";
						$columnIndex=0;
						$rowIndex++;
					}else {
						$addChar=$char;
					}
				}
			}
			if($addChar!=""){
				$fieldValue.=$addChar;

			}
		}

		if($fieldValue) { // save last field
			$array[$rowIndex][$columnIndex] = $fieldValue;
		}
		return $array;
	}
	
	function parse_csv() {
		$arr = csvstring_to_array(file_get_contents('lista-aptek.csv'));
		// echo ltrim($arr[1][0]);
		 // print_r($arr);
		
		foreach($arr as $key=>$var) {
			
			$arr2[] = array(gcode($var[3],$var[2],$var[1]),ltrim($var[0]));
			}
		
		return $arr2; // array2[0] - geocode arr2[1] - id_doz
	}
		
	print_r(parse_csv());
?>