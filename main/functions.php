<?php

function load_class($class, $ext = false){
	if(!class_exists($class)){
		if($ext == false){
			include_once ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'class.'.$class.'.php';
		}else{
			include_once ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'ext'. DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR .'class'.DIRECTORY_SEPARATOR.'class.'.$class.'.php';
		}
	}
}

/*
Name: Stephen Kennedy
Date: 12/4/19
Comment: This is the core function behind the CMV structure of Vogon. It solves one of the things I hated about seeing this structure in OpenCart.

By including the documents in this fashion, each document is treated as a dynamically assigned function definition. The app doesn't have to load anything but the controllers, models, and views that are called by the route logic, meaning we only really have to slow the app down when we're doing slow tasks.

But the biggest benefit, and the thing I keep raving to myself about, is how clean it makes the sub-files. Many of them are only a few lines long, and it's all procedural programming, so it's very easy to write and follow.

The biggest downside is the additional mental overhead of keeping track of the logic of what is being called. Instead of getting a clear idea of the logic of a piece of code, you have to cross-reference several documents. In my experience with it, the positives have far outweighed the negatives, as breaking tasks into small reusable chunks of code has helped me to sub-divide tasks that seem to be initially complex into a series of simple actions that can be independently debugged.
*/

function loader($file, $data = []){
	global $db;
	foreach($data as $var => $val){
		$$var = $val;
	}
	if(file_exists($file)){ //this way if we call a controller that doesn't exist, we don't error
		return include $file;
	}else{
		//echo 'unable to load '.$file;
		return null; //Should probably look into returning some kind of non-exception style error, just so the app can give the user some feedback on why their request didn't follow the app's pre-programmed logic.
	}
}

function load_view($view, $data = [], $ext = false){
	ob_start();
	if($ext == false){
		loader(ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'view.'.$view.'.php', $data);
	}else{
		loader(ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'ext'.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'view.'.$view.'.php', $data);
	}
	return ob_get_clean();
}
function load_model($model, $data = [], $ext = false){
	if($ext == false){
		return loader(ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'model.'.$model.'.php', $data);
	}else{
		return loader(ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'ext'.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'model.'.$model.'.php', $data);
	}
}
function load_controller($controller, $data = [], $ext = false){
	if($ext == false){
		return loader(ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'controller.'.$controller.'.php', $data);
	}else{
		return loader(ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'ext'.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.'controller.'.$controller.'.php', $data);
	}
}
function debug_d($var){
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}
function dir_contents($dir, $ext_filter = false){
	$return = array_diff(scandir($dir), ['.', '..']);
	if($ext_filter != false){
		foreach($return as $key => $entry){
			$len = strlen($ext_filter) * -1;
			if(substr($entry, $len) != $ext_filter){
				unset($return[$key]);
			}
		}
		//Sort because we culled entries
	}
	sort($return);
	return $return;
}
/*
Name: Stephen Kennedy
Date: 10/5/19
Comment: Frequently, we're parsing the URI to determine what actions the controller will take, but since this whole system is build around the idea that slugs and slug_lengths are variable and easily changed by the user, we need a systemic way to get parts of the slug to enable the same functionality without manually parsing.
*/
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

/*
Name: Stephen Kennedy
Date: 10/5/19
Comment: By the same token, we need an easy way to build a slug.
*/
function build_slug($uri, $params = [], $ext = false){
	global $db;
	$string = URI;
	if($ext != false){
		$sql = 'SELECT * FROM route WHERE route_ext = :ext AND ext_primary = 1';
		$db_params = [':ext' => $ext];
		$query = $db->query($sql, $db_params);
		if($query != false){
			$result = $query->fetch();
			$string .= '/'. $result['route_slug'];
		}
	}
	$safe = [];
	$string .= '/'.$uri;
	if(count($params) > 0){
		foreach($params as $key => $value){
			if(gettype($value) == 'string'){
				$safe[] = urlencode($key) . '=' . urlencode(trim($value));
			}
		}
		$safe = implode('&', $safe);
		$string .= '?'.$safe;
	}
	return $string;
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
	if(gettype($date) != 'int'){
		$date = strtotime($date);
	}
	return date('m/d/Y g:ia', $date);
}
function db_date($date){
	if(gettype($date) != 'int'){
		$date = strtotime($date);
	}
	return date('Y-m-d H:i:s', $date);
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
?>