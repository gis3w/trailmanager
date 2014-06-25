<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Data_Itinerary extends Controller_Ajax_Data_Base{
    
    protected $_pagination = FALSE;
    
    protected $_thumb_uri ="/download/imageitinerary/thumbnail/";
    
    
    protected function _single_request_row($orm) {
        return $this->_get_base_data_from_orm($orm);
        
    }
  
    
}