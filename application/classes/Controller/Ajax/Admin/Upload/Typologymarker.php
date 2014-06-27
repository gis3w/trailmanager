<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Admin_Upload_Typologymarker extends Controller_Ajax_Admin_Base_Upload {
    
    public $uplload_options = array(
        'param_name' => 'marker',
        'image_versions' => array(
            'thumbnail' => array()
        )
    );
   
}