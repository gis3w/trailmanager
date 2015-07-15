<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Upload_Imagehighlitingpoi extends Controller_Ajax_Admin_Base_Upload {
    
    protected $_exeLogin = FALSE;
    
    protected $_download_url = "/download";
    
    protected $_base_ajax_url = 'jx/';
        
    public $uplload_options = array(
        'param_name' => 'front_image_highliting_poi',
    );
   
}