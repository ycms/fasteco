<?php namespace YC\Theme2015\Http\Controllers;

use YCMS\Modules\Routing\Controller;

class Theme2015Controller extends Controller {
	
	public function index()
	{
		return view('theme2015::index');
	}
	
}