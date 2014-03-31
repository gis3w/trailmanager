<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Admin_Home extends Controller_Auth_Strict {
    
    public $tcontent ="admin/home";
    public $jspre = "BOOTSTRAP_URL='/jx/admin/config'";

    public function action_index(){}
    
}