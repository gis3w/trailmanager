<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Geo_Area extends Controller_Ajax_Geo_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Area";
    
    
    protected function _single_request_row($orm) {

        return $this->_get_geo_base_data_from_orm($orm);
        
    }
  
    
}