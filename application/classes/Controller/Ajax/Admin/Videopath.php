<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Videopath extends Controller_Ajax_Base_Crud_GET{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Video_Path";
    
  
    protected function _edit() {
        
        $this->_validation_orm();
    }
    
    public function action_delete(){}
}