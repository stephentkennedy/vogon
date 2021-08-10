<?php
$request = explode('?', $_SERVER['REQUEST_URI'])[0];
$trim = strlen(URI);
if(substr($request, 0, $trim) == URI){
	$request = substr($request, $trim);
}
$request = explode('/', $request);
$slug = @$request[1];
$sql = 'SELECT * FROM route WHERE route_slug = :slug';
$params = [
	':slug' => $slug
];
$route = $db->query($sql, $params);
if($db->error == false){
	$route = $route->fetchAll();
	$route = $route[0];
	if(empty($route['route_ext'])){
		load_controller($route['route_controller']);
	}else{
		load_controller($route['route_controller'], [], $route['route_ext']);
	}
}else{
	echo $db->error->getMessage();
}