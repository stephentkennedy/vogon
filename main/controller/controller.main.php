<?php
	$api_response = load_model('item', ['id' => 12091], 'runescape_api');
	echo load_view('item', $api_response, 'runescape_api');
?>