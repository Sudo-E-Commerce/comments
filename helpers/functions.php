<?php 
/**
 * Lấy ký tự đầu tiên trong chuỗi
 * @param string 		$str: chuỗi cần lấy
 * @return string
 */
function getNameKey($str) {
	$str_array = explode(' ', $str);
	$name = $str_array[count($str_array)-1];
	$i = 0;
	while ($name == '') {
		$i++;
		$name = $str_array[count($str_array)-$i];
	}
    return strtoupper(substr($name, 0, 1));
}