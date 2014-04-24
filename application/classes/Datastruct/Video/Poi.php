<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Video_Poi extends Datastruct {
    
    protected $_nameORM = "Video_Poi";
    
    public static $preKeyField = 'videopoi';

    public $sortable = TRUE;
    
    public $icon = 'suitcase';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'poi-data',
            'position' => 'left',
            'fields' => array('id','title','description','embed'),
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
                "description" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
                "poi_id" => array(
                    'form_input_type' => self::HIDDEN,
                    "subform_table_show" => FALSE,
                ),
                
                 "embed" => array(
                    'form_input_type' => self::TEXTAREA,
                ),
                "data_ins" => array(
                    'form_show' => FALSE,
                    'subform_table_show' => FALSE,
                ),
                "data_mod" => array(
                    'form_show' => FALSE,
                    'subform_table_show' => FALSE,
                ),
                "norder" => array(
                    'form_show' => FALSE,
                    'subform_table_show' => FALSE,
                ),
                
            );
      }               
        
    
}
