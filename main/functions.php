<?php

//Vogon Core Functions
function load_file($file, $parameters = []){
	global $db;
	foreach($parameters as $var => $val){
		$$var = $val;
	}
	if(file_exists($file)){
		$data = $parameters; //For compatibility, in future updates this will be removed.
		return include $file;
	}else{
		return null;
	}
}

function load_class($class, $ext = false){
	if(!class_exists($class)){
		if($ext == false){
			$class_file = ROOT . DIRECTORY_SEPARATOR . 'main' .DIRECTORY_SEPARATOR . 'class' . DIRECTORY_SEPARATOR . 'class.' . $class . '.php';
		}else{
			$class_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR .'class'. DIRECTORY_SEPARATOR . 'class.' . $class . '.php';
		}
		if(file_exists($class_file)){
			load_file($class_file);
		}
	}
}

function load_view($view, $parameters = [], $ext = false){
	ob_start();
	if($ext == false){
		$view_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'view.' . $view . '.php';
	}else{
		$view_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'view.' . $view . '.php';
	}
	load_file($view_file, $parameters);
	return ob_get_clean();
}

function load_model($model, $parameters = [], $ext = false){
	if($ext == false){
		$model_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . 'model.' . $model . '.php';
	}else{
		$model_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . 'model.' . $model . '.php';
	}
	return load_file($model_file, $parameters);
}
function load_controller($controller, $parameters = [], $ext = false){
	if($ext == false){
		$controller_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'controller.' . $controller . '.php';
	}else{
		$controller_file = ROOT . DIRECTORY_SEPARATOR . 'main' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'controller.' . $controller . '.php';
	}
	return load_file($controller_file, $parameters);
}

//General Functions
function debug_d($var){ //Compatibility shim, new calls should be to debug_dump();
	debug_dump($var);
}

function debug_dump($var){
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}

function dir_contents($dir, $extension_filter = false){
	$remove_from_results = [
		'.',
		'..'
	];
	$directory_contents = scandir($dir);
	$to_return = array_diff($directory_contents, $remove_from_results);
	if($extension_filter != false){
		filter_array_of_filenames_by_extension($to_return, $extension_filter);
	}
	sort($to_return);
	return $to_return;
}
function filter_array_of_filenames_by_extension($items, $extension){
	foreach($items as $key => $entry){
		if(!compare_extension($entry, $extension)){
			unset($items[$key]);
		}
	}
	return $items;
}

function compare_extension($filename, $extension){
	$len = strlen($extension) * -1;
	if(substr($filename, $len) != $extension){
		return false;
	}
	return true;
}

function get_slug_part($requested = 'ext', $slug = false){
	if($slug == false){
		$slug = $_SERVER['REQUEST_URI'];
	}
	$slug = explode('?', $slug)[0];
	$slug = str_replace(URI, '', $slug);
	if($requested == 'ext'){
		$requested = 0;
	}
	$slug = ltrim($slug, '/');
	$slug = explode('/', $slug);
	if(isset($slug[(int) $requested])){
		return $slug[(int) $requested];
	}else{
		return false;
	}
}

function build_slug($uri, $params = [], $ext = false){
	$slug_to_return = URI;
	if($ext != false){
		$ext_slug = get_ext_slug($ext);
		if(!empty($ext_slug)){
			$slug_to_return .= '/'. $ext_slug;
		}
	}
	$slug_to_return .= '/'.$uri;
	if(count($params) > 0){
		$slug_to_return .= urlencode_get_values($params);
	}
	return $slug_to_return;
}

function get_ext_slug($ext){
	global $db;
	$sql = 'SELECT * FROM route WHERE route_ext = :ext AND ext_primary = 1';
	$db_params = [':ext' => $ext];
	$query = $db->query($sql, $db_params);
	if($query != false){
		return $query->fetch()['route_slug'];
	}else{
		return false;
	}
}

function urlencode_get_values($array){
	$encoded_items_array = [];
	foreach($array as $key => $value){
		$encoded_key = urlencode($key);
		$encoded_value = urlencode($value);
		$encoded_items_array[] = $encoded_key . '=' . $encoded_value;
	}
	$encoded_items_string = implode('&', $encoded_items_array);
	$encoded_items_string = '?'.$encoded_items_string;
	return $encoded_items_string;
}

function get_var($var_name, $format="string"){
	global $db;
	$sql = "SELECT var_content FROM var WHERE var_name = :name";
	$params = [':name' => $var_name];
	$query = $db->query($sql, $params);
	$r = $query->fetch();
	$content = $r['var_content'];
	switch($format){
		case 'array':
			$content = unserialize($content);
			break;
		default:
			break;
	}
	return $content;
}

function put_var($var_name, $value, $format="string"){
	global $db;
	$sql = "SELECT var_content FROM var WHERE var_name = :name";
	$params = [':name' => $var_name];
	$query = $db->query($sql, $params);
	switch($format){
		case 'array':
			$content = serialize($content);
			break;
		default:
			break;
	}
	$params = [
		':content' => $content,
		':name' => $name
	];
	if($query != false){
		$sql = 'UPDATE var SET var_content = :content WHERE var_name = :name';
		$db->query($sql, $params);
	}else{
		$sql = 'INSERT INTO var (var_session, var_name, var_content, var_type, user_key) VALUES (0, :name, :content, "", 0)';
		$db->query($sql, $params);
	}
}

function slugify($content){
	$content = trim($content);
	$content = preg_replace('/[^a-zA-Z0-9]/', '_', $content);
	$content = preg_replace('/_{2,}/', '_', $content);
	$content = trim($content, '_');
	return strtolower($content);
}

function nice_date($date){
	return reformat_date($date, 'm/d/Y g:ia');
}

function db_date($date){
	return reformat_date($date, 'Y-m-d H:i:s');
}

function reformat_date($date, $format){
	if(gettype($date) != 'int'){
		$date = strtotime($date);
	}
	return date($format, $date);
}

function recursiveScan($loc, $all = false){
	$array = scandir($loc);
	$return = [];
	foreach($array as $file){
		if($file != '.' && $file != '..'){
			if(is_dir($loc. DIRECTORY_SEPARATOR .$file)){
				$newArray = recursiveScan($loc. DIRECTORY_SEPARATOR .$file, $all);
				foreach($newArray as $item){
					$return[] = $item;
				}
			}else{
				if($all == false){
					if(stristr($file, '.php') !== false){
						$return[] = $loc. DIRECTORY_SEPARATOR .$file;
					}
				}else{
					$return[] = $loc. DIRECTORY_SEPARATOR .$file;
				}
			}
		}
	}
	return $return;
}
