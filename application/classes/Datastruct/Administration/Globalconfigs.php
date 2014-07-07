<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Administration_Globalconfigs extends Datastruct {
    
    protected $_nameORM = "Global_Config";
    
     public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "paroramet",
            
        )
    );
    
    public $groups = array(
        array(
            'name' => 'globalconfigs-data',
            'position' => 'left',
            'fields' => array('id','paramentro','valore','to_config'),
        ),
    );
    
}
  