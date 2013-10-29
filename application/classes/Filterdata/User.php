<?php defined('SYSPATH') or die('No direct script access.');

class Filterdata_User extends Filterdata {
    
    
    protected function _initialize() {
        parent::_initialize();
        
        $this->add_filter("nome",$this->_build_param(self::INPUT,__("name")));
        $this->add_filter("cognome",$this->_build_param(self::INPUT,__("surname")));
        
        $this->_set_role();
        
      
               
    }
    
    protected function _set_role()
    {
        $fondi = ORM::factory('Role')
                ->where('name','!=','login')
                ->order_by('level')
                ->find_all();
        
        // si deve aggiungere i filtri in base all'utente collegato
        $values = $this->_build_values($fondi, "name", "id");
        $this->add_filter("role_id", $this->_build_param(self::SELECT,__("role"),$values));
    }
}