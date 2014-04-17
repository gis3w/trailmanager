<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Data_Path extends Controller_Ajax_Data_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Path";
    
    
    protected function _single_request_row($orm) {
        return $this->_get_base_data_from_orm($orm);
        
    }
  
    
}