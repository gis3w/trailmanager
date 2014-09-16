<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Admin_Upload_Pathmodeicon extends Controller_Ajax_Admin_Base_Upload {
    
    public $uplload_options = array(
        'param_name' => 'icon',
        'image_versions' => array(
            'thumbnail' => array()
        )
    );
   
}