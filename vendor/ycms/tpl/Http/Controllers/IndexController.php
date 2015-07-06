<?php namespace YC\Tpl\Http\Controllers;

use YCMS\Modules\Routing\Controller;

class IndexController extends Controller {
	
	public function index()
	{
		return view('tpl::index');
	}
	
}