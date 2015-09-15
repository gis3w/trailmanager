<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Administration_Diffsegments extends Controller_Ajax_Admin_Administration_Base{
    
    protected $_pagination = FALSE;
    protected $_datastruct = "Administration_Diffsegments";

    public function action_create()
    {
        $this->_orm->code = $_POST['code'];
        parent::action_create();
    }
}