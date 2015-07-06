<?php namespace YC\Base\Http\Controllers;

use YCMS\Modules\Routing\Controller;

class BaseController extends Controller {
	
	public function index()
	{
		return view('base::index');
	}
	
}