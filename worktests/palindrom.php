<?php

$string = "Аргентина манит негра Sum summus mus";
$string_arr = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
$found_start = 0;
$found_end = 0;
$result = '';
$result_arr = array ();
$string_end = count($string_arr) - 1;

for ($end = $string_end; $end > 0; $end--){
	$s = 0;
	$e = $end;
	while ($s <= $end){
		if (trim($string_arr[$s]) === ''){
			//print("Пропускаем пробел в начале\n");
			$s++;
			continue;
		}
		if (trim($string_arr[$e]) === ''){
			//print("Пропускаем пробел в конце\n");
			$e--;
			continue;
		}
		if ($found_end && $s >= $e){
			for ($f = $found_start; $f <= $found_end; $f++){
				$result .= $string_arr[$f];
			}
			// print('----- Есть результат ' . $result . " -----\n");
			$result_arr[] = $result;
			$result = '';
			$s = $found_start + 1;
			$e = $end;
			$found_start = 0;
			$found_end = 0;
			continue;
		}
		// print('Сравниваем ' . $string_arr[$s] . '(' . $s . ') и ' . $string_arr[$e] . '(' . $e . ")\n");
		if (mb_strtolower($string_arr[$s], 'UTF-8') == mb_strtolower($string_arr[$e], 'UTF-8')){
			// print("Совпадение найдено\n");
			if (!$found_end){
				$found_start = $s;
				$found_end = $e;
			}
			$e--;
		}else{
			// print("Расхождение найдено\n");
			if ($found_end){
				$found_start = 0;
				$found_end = 0;
			}
			$e = $end;
		}
		$s++;
	}
}
usort($result_arr, 'sort_by_len');
// print_r($result_arr);
function sort_by_len($f, $s){
	return !(mb_strlen($f, 'UTF-8') > mb_strlen($s, 'UTF-8'));
}
if (empty ($result_arr)){
	print(reset($string_arr) . "\n");
}else{
	print(reset($result_arr) . "\n");
}
