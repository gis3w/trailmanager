<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Media_Poi extends Controller_Ajax_Media_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Image_Poi";



    protected function _single_request_row($orm) {
        return $this->_get_base_data_from_orm($orm);
        
    }
  
    
}