<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Global_Typology extends Datastruct {
    
    protected $_nameORM = "Typology";
    
    public $icon = 'suitcase';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'globla-typology-data',
            'position' => 'left',
            'fields' => array('id','name','description'),
        ),
       
    );
    
    public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "name"
        )
    );
    
     protected function _columns_type() {
        
            return array(
                "description" => array(
                    'form_input_type' => self::TEXTAREA,
                ),
            );
      }
     
    
}
