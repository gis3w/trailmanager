<?php defined('SYSPATH') or die('No direct script access.');

class Filterdata_Company extends Filterdata {
    
    
    protected function _initialize() {
        parent::_initialize();
        
        $this->add_filter("ragione_sociale",$this->_build_param(self::INPUT,__("Corporate name")));
        $this->add_filter("nome_commerciale",$this->_build_param(self::INPUT,__("Commercial name")));
        
        $this->_set_fondi();
        
      
               
    }
    
    protected function _set_fondi()
    {
        $fondi = ORM::factory('Fondo_Interprofessionale')->find_all();
        $values = $this->_build_values($fondi, "nome", "id");
        $this->add_filter("fondo_interprofessionale_id", $this->_build_param(self::SELECT,__("Interprofessional found"),$values));
    }
}