<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Global_Typology extends Datastruct {
    
    protected $_nameORM = "Typology";
    
    public $icon = 'suitcase';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'globlal-typology-data',
            'position' => 'left',
            'fields' => array('id','name','description','icon'),
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
                "icon" => array(
                    "form_input_type" => self::INPUT,
                    "multiple" => FALSE,
                    "data_type" => self::FILE,
                    "form_show" => TRUE,
                    "table_show" => FALSE,
                   "subform_table_show" => TRUE, 
                    'label' =>__('Icon'),
                    'urls' => array(
                        'data' => 'jx/admin/upload/typologyicon',
                        'delete' => 'jx/admin/upload/typologyicon?file=$1',
                        'delete_options' => array(
                           '$1' => self::$preKeyField.'-file',
                        ),
                        'download' => 'admin/download/typologyicon/index/$1',
                        'download_options' => array(
                            '$1' => self::$preKeyField.'-file',
                            ),
                        'thumbnail' => 'admin/download/typologyicon/thumbnail/$1',
                        'thumbnail_options' => array(
                            '$1' => self::$preKeyField.'-file',
                            ),
                    ),  
                ),
//                "marker" => array(
//                    "form_input_type" => self::INPUT,
//                    "multiple" => FALSE,
//                    "data_type" => self::FILE,
//                    "form_show" => TRUE,
//                    "table_show" => FALSE,
//                   "subform_table_show" => TRUE, 
//                    'label' =>__('Marker'),
//                    'urls' => array(
//                        'data' => 'jx/admin/upload/typologymarker',
//                        'delete' => 'jx/admin/upload/typologymarker?file=$1',
//                        'delete_options' => array(
//                           '$1' => self::$preKeyField.'-file',
//                        ),
//                        'download' => 'admin/download/typologymarker/index/$1',
//                        'download_options' => array(
//                            '$1' => self::$preKeyField.'-file',
//                            ),
//                        'thumbnail' => 'admin/download/typologymarker/thumbnail/$1',
//                        'thumbnail_options' => array(
//                            '$1' => self::$preKeyField.'-file',
//                            ),
//                    ),  
//                ),
            );
      }
      
      
     
    
}
