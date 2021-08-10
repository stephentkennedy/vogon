<?php
//find and define ROOT Constant
$directories = explode(DIRECTORY_SEPARATOR , __DIR__);
array_pop($directories);
$directories = implode(DIRECTORY_SEPARATOR , $directories);
define('ROOT', $directories);

//Autoload classes from the auto folder.
$autoload_folder = __DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'auto';
$auto_classes = scandir($autoload_folder);
foreach($auto_classes as $class_file){
	if($class_file != '.' && $class_file != '..'){
		$class_name = str_replace(['class.', '.php'], '', $class_file);
		if(!class_exists($class_name)){
			include_once __DIR__ . DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'auto' . DIRECTORY_SEPARATOR . $class_file;
		}
	}
}

//Set Contants From Settings
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