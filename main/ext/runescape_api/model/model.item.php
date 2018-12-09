<?php
$bot = new fish;
$bot->url = 'http://services.runescape.com/m=itemdb_rs/api/catalogue/detail.json?item='.$id;
$bot->dispatch();
$bot->json();
return $bot->parsed;
?>