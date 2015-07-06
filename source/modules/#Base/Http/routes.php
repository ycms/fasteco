<?php

Route::group(['prefix' => 'base', 'namespace' => 'YC\Base\Http\Controllers'], function()
{
	Route::get('/', 'BaseController@index');
});