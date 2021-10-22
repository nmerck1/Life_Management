<?php

spl_autoload_register(function($classname) {
		$ext = ".class";
    $filename = str_replace("\\", "/", $classname).$ext.".php";
		$path = "../classes/";

		$full_path = $path.$filename;

		if (!file_exists($full_path)){
			return false;
		}
    require_once $full_path;

});







?>
