<?php defined('SYSPATH') or die('No direct script access.');

class Filterdata_Chemical extends Filterdata {
    
    
    protected function _initialize() {
        parent::_initialize();
        
        $this->add_filter("nome",$this->_build_param(self::INPUT,__("Name")));
       
      
               
    }
    
    
}