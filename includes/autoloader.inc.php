<?php

spl_autoload_register(function($classname) {
		$ext = ".class";
		$lowercase = strtolower($classname);
    $filename = str_replace("\\", "/", $lowercase).$ext.".php";
		$path = "../classes/";
		$path2 = "classes/";

		$is_server = false;

		if ($is_server == true) {
			$path = "/home3/lifement/public_html/classes/";
		}
		$full_path1 = $path.$filename;
		$full_path2 = $path2.$filename;
	//	echo "full_path1: ".$full_path1."<br>";
	//	echo "full_path2: ".$full_path2."<br><br>";
		//echo "filename: ".$filename."<br>";

		if (file_exists($full_path1)){
			//echo "first full path exists, requiring it <br>";
			require_once $full_path1;
			return true;
		} elseif(file_exists($full_path2)) {
			//echo "second full path exists, requiring it <br>";
			require_once $full_path2;
			return true;
		} else {
			//echo "None of the full paths were correct! (returning false) <br>";
			return false;
		}

});







?>
