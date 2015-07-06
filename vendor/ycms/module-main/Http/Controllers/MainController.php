<?php namespace YC\Main\Http\Controllers;

use YCMS\Modules\Routing\Controller;

class MainController extends Controller {
	
	public function index()
	{
        static $wp_did_header;

        global $wpdb, $current_site;

        define('WP_ONLY', true);

        if(!defined('ABSPATH')){

            if (!isset($wp_did_header)) {

                define('WP_USE_THEMES', true);


                $wp_did_header = true;

                require_once(dirname(base_path()) . '/app/article/wp-load.php');

                wp();

                require_once(dirname(base_path()) . '/app/template-loader.php');

            }
            //require_once dirname(base_path()) . '/app/article/index.php';
        }
        return view('main::index');
	}
	
}