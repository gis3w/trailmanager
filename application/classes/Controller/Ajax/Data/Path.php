<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Data_Path extends Controller_Ajax_Data_Base{
    
    protected $_pagination = FALSE;
    
    
    protected $_thumb_uri ="/download/imagepath/thumbnail/";
    
    protected function _single_request_row($orm) {
        $toRes = $this->_get_base_data_from_orm($orm);
        
        $toRes['modes'] = array_keys($orm->modes->find_all()->as_array('id'));
        
        $toRes['altitude_gap'] = $toRes['altitude_gap'].' km';
        $toRes['length'] = $toRes['length'].' km';
        
        return $toRes;
        
    }
  
    
}