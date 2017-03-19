<?php

use \GF\Components\Route;
use \GF\Components\Response;

Route::get('/', ["Controllers.Main", "index"]);
Route::get('main', function(){
	echo "main";
});