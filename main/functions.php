<?php

function load_class($class){
	include ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'class'.DIRECTORY_SEPARATOR.'class.'.$class.'.php';
}

function loader($file, $data){
	global $db;
	foreach($data as $var => $val){
		$$var = $val;
	}
	return include $file;
}

function load_view($view, $data = [], $ext = false){
	if($ext == false){
		return loader(ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'view.'.$view.'.php', $data);
	}else{
		return loader(ROOT.DIRECTORY_SEPARATOR.'main'.DIRECTORY_SEPARATOR.'ext'.DIRECTORY_SEPARATOR.$ext.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'view.'.$view.'.php', $data);
	}
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

?>