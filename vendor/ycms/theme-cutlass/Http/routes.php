<?php

Route::group(['prefix' => 'cutlass', 'namespace' => 'YC\Cutlass\Http\Controllers'], function()
{
	Route::get('/', 'CutlassController@index');
});