<?php namespace YC\Tpl\Http\Controllers;

use YCMS\Modules\Routing\Controller;

class TplController extends Controller {
	
	public function index()
	{
		return view('tpl::index');
	}
	
}