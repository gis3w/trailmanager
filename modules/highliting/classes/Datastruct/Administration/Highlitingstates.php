<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Administration_Highlitingstates extends Datastruct {
    
    protected $_nameORM = "Highliting_State";
    
     public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "name",
            
        )
    );
    
    public $groups = array(
        array(
            'name' => 'highlitingstates-data',
            'position' => 'left',
            'fields' => array('id','name','description','color'),
        ),
    );
  
    protected function _columns_type()
    {
        return array(
            "color" => array(
                            "form_input_type" => self::MAPBOX_COLOR,
                            "class" => "color-highlitingstate",
                        ),
        );
    }



}
  