<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Administration_Pathmodes extends Datastruct {
    
    protected $_nameORM = "Path_Mode";
    
     public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "mode",
            
        )
    );
    
    public $groups = array(
        array(
            'name' => 'Pathmodes-data',
            'position' => 'left',
            'fields' => array('id','mode','description','icon'),
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
  