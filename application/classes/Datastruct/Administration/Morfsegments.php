<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Administration_Morfsegments extends Datastruct {

    
    protected $_nameORM = "Morf_Segment";
    
     public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "description",
            
        )
    );
    
    public $groups = array(
        array(
            'name' => 'Morfsegment-data',
            'position' => 'left',
            'fields' => array('code','description','definition'),
        ),
    );
    
     protected function _columns_type() {
        
        return array(
            "code" => array(
                "editable" => TRUE,
            ),
            "definition" => array(
                "form_input_type" => self::TEXTAREA
            ),
        );
    }  
    
}
  