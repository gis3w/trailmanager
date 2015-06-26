<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Administration_Classrilsegments extends Datastruct {

    
    protected $_nameORM = "Class_Ril_Segment";
    
     public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "class",
            
        )
    );
    
    public $groups = array(
        array(
            'name' => 'Classrilsegments-data',
            'position' => 'left',
            'fields' => array('class','description'),
        ),
    );
    
     protected function _columns_type() {
        
        return array(
            "class" => array(
                "editable" => TRUE,
            ),
            "description" => array(
                "form_input_type" => self::TEXTAREA
            ),
        );
    }  
    
}
  