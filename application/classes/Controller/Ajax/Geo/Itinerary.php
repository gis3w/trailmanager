<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Geo_Itinerary extends Controller_Ajax_Geo_Base{
    
    protected $_pagination = FALSE;

    
    
    protected function _single_request_row($orm) {

        return $this->_get_geo_base_data_from_orm($orm);
        
    }
  
    
}