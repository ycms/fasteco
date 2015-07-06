<?php

Route::group(['prefix' => 'main', 'namespace' => 'YC\Main\Http\Controllers'], function()
{
    Route::get('/', 'MainController@index');
    Route::any('{all?}', 'MainController@index')->where('all', '.+');
});