<?php defined('SYSPATH') or die('No direct script access.');

class Filterdata_Productionunit extends Filterdata {
    
    
    protected function _initialize() {
        parent::_initialize();
        
        $this->add_filter("denominazione",$this->_build_param(self::INPUT,__("denominazione")));
        $this->_set_tipi();
        
      
               
    }
    
    protected function _set_tipi()
    {
        $tipi = ORM::factory('Tipo_Unita_Produttiva')->find_all();
        $values = $this->_build_values($tipi, "tipo", "id");
        $this->add_filter("tipo_id", $this->_build_param(self::SELECT,__("tipo_unita_produttiva"),$values));
    }
}