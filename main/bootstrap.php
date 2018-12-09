<?php
/*
Name: Stephen Kennedy
Date: 12/4/2018
Comment: Let's establish what we need for our bootstrap;
*/
$directories = explode(DIRECTORY_SEPARATOR,__DIR__);
ini_set('display_errors', 1);
array_pop($directories);
$directories = implode(DIRECTORY_SEPARATOR,$directories);
define('ROOT', $directories);

//Autoload classes from the auto folder.
$auto_classes = scandir(__DIR__.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'auto');
foreach($auto_classes as $class){
	if($class != '.' && $class != '..'){
		include __DIR__.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'auto'.DIRECTORY_SEPARATOR.$class;
	}
}

//Define the constants from our settings
$settings = parse_ini_file(__DIR__.DIRECTORY_SEPARATOR.'config.ini', true);
$settings = $settings['app_constants'];
foreach($settings as $const => $val){
	define(strtoupper($const), $val);
}
unset($settings);

//Create our Database Connection
$db = new thumb(__DIR__.DIRECTORY_SEPARATOR.'config.ini');

//Include our framework functions
include __DIR__.DIRECTORY_SEPARATOR.'functions.php';

//Include our router
include __DIR__.DIRECTORY_SEPARATOR.'router.php';
?>