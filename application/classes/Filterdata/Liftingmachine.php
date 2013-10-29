<?php defined('SYSPATH') or die('No direct script access.');

class Filterdata_Liftingmachine extends Filterdata {
    
    
    protected function _initialize() {
        parent::_initialize();
        
        $this->add_filter("marca",$this->_build_param(self::INPUT,__("Brand")));
        $this->add_filter("modello",$this->_build_param(self::INPUT,__("Model")));  
        $this->add_filter("targa",$this->_build_param(self::INPUT,__("Number plate")));  
        $this->add_filter("anno",$this->_build_param(self::INPUT,__("Year")));
      
               
    }
    
    
}