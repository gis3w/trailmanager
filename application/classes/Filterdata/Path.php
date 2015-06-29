<?php defined('SYSPATH') or die('No direct script access.');

class Filterdata_Path extends Filterdata {
    
    
    protected function _initialize() {
        parent::_initialize();
        
        $this->add_filter("se",$this->_build_param(self::INPUT,__("Path name")));

      
               
    }

}