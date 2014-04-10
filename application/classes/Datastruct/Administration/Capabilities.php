<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Administration_Capabilities extends Datastruct {
    
    protected $_nameORM = "Capability";
    
     public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "name",
            
        )
    );
    
    public $groups = array(
        array(
            'name' => 'capabilities-data',
            'position' => 'left',
            'fields' => array('id','name','description'),
        ),
        array(
            'name' => 'foreign-capabilities-data',
            'position' => 'right',
            'fields' => array('roles'),
        )
      
    );
    
     protected function _columns_type() {
        
        return array(
            "description" => array(
                "form_input_type" => self::TEXTAREA
            ),
        );
    }
    
      
    protected function _foreign_column_type() {
      
        $fct = array();
        
        //automezzo_tipo
        $fcolumn = $this->_columnStruct;
        $fcolumn = array_replace($fcolumn,array(
            'data_type' => 'integer',
            'form_input_type' => self::SELECT,
            'foreign_key' => 'role',
            'foreign_toshow' => '$1',
            'foreign_toshow_params' => array(
                        '$1' => 'name',
                    ),
            'foreign_mode' => self::MULTISELECT,
            'label' => __('Roles')
        ));
        
        $fct['roles'] = $fcolumn;

      
        
        return $fct;
        
    }
    
}
  