<?php
	$output = '';
	$output .= '<h1>'.$item['name'].'</h1>';
	$output .= '<img src="'.$item['icon'].'"><img src="'.$item['icon_large'].'"><br>';
	$output .= '<h2>'.$item['type'].'</h2>';
	$output .= '<p>'.$item['description'].'</p>';
	return $output;
?>