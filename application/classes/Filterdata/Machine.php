<?php defined('SYSPATH') or die('No direct script access.');

class Filterdata_Machine extends Filterdata {
    
    
    protected function _initialize() {
        parent::_initialize();
        
        $this->add_filter("marca",$this->_build_param(self::INPUT,__("Brand")));
        $this->add_filter("modello",$this->_build_param(self::INPUT,__("Model")));  
        $this->add_filter("matricola",$this->_build_param(self::INPUT,__("Identification number")));  
        $this->add_filter("anno",$this->_build_param(self::INPUT,__("Year")));
      
               
    }
    
    
}