<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Logout extends Controller_Ajax_Main{
    
    protected $_vorm;
    public $vErrors = array();


    public function action_update()
    {
        Auth::instance()->logout(TRUE);
    }
    
}