<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Global_Page extends Datastruct {
    
    protected $_nameORM = "Page";
    
    public $icon = 'suitcase';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'global-page-data',
            'position' => 'block',
            'fields' => array('id','alpha_id','title','body'),
        ),
       
    );
    
    public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "title"
        )
    );
    
     protected function _columns_type() {
        
            return array(
                "body" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
            );
      }
      
      
     
    
}
