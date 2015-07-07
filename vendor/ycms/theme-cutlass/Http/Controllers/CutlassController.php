<?php namespace YC\Cutlass\Http\Controllers;

use YCMS\Modules\Routing\Controller;

class CutlassController extends Controller {
	
	public function index()
	{
		return view('cutlass::index');
	}
	
}