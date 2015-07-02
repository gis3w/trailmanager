<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Administration_Insediampois extends Datastruct {

    
    protected $_nameORM = "Insediam_Poi";
    
     public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "description",
            
        )
    );
    
    public $groups = array(
        array(
            'name' => 'Insediampois-data',
            'position' => 'left',
            'fields' => array('code','description'),
        ),
    );
    
     protected function _columns_type() {
        
        return array(
            "code" => array(
                "editable" => TRUE,
            ),
            "description" => array(
                "form_input_type" => self::TEXTAREA
            ),
        );
    }  
    
}
  