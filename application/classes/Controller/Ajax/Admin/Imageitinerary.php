<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Imageitinerary extends Controller_Ajax_Base_Crud{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Image_Itinerary";
    
    
    protected function _edit() {
        
        $this->_validation_orm();
    }
    
    public function action_delete(){}
  
}