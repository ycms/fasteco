<?php

Route::group(['prefix' => 'tpl', 'namespace' => 'YC\Tpl\Http\Controllers'], function()
{
	Route::get('/', 'TplController@index');
});