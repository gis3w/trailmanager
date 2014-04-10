<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Administration_Roles extends Datastruct {
    
    protected $_nameORM = "Role";
    
     public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "name",
            
        )
    );
    
    public $groups = array(
        array(
            'name' => 'roles-data',
            'position' => 'left',
            'fields' => array('id','name','description','level'),
        ),
    );
    
     protected function _columns_type() {
        
        return array(
            "description" => array(
                "form_input_type" => self::TEXTAREA
            ),
        );
    }
    
    
}
  