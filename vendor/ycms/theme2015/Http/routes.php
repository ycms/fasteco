<?php

Route::group(['prefix' => 'theme2015', 'namespace' => 'YC\Theme2015\Http\Controllers'], function()
{
	Route::get('/', 'Theme2015Controller@index');
});