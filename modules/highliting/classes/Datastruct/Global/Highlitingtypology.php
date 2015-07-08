<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Global_Highlitingtypology extends Datastruct {
    
    public $enctype = self::ECNTYPE_MULTIPART;
    
    protected $_nameORM = "Highliting_Typology";
    
    public $icon = 'suitcase';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'global-highlitingtypology-data',
            'position' => 'left',
            'fields' => array('id','name','description','icon','marker'),
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
                        'data' => 'jx/admin/upload/highlitingtypologyicon',
                        'delete' => 'jx/admin/upload/highlitingtypologyicon?file=$1',
                        'delete_options' => array(
                           '$1' => 'icon',
                        ),
                        'download' => 'admin/download/highlitingtypologyicon/index/$1',
                        'download_options' => array(
                            '$1' => 'icon',
                            ),
                        'thumbnail' => 'admin/download/highlitingtypologyicon/thumbnail/$1',
                        'thumbnail_options' => array(
                            '$1' =>'icon',
                            ),
                    ),  
                ),
                "marker" => array(
                    "form_input_type" => self::INPUT,
                    "multiple" => FALSE,
                    "data_type" => self::FILE,
                    "form_show" => TRUE,
                    "table_show" => FALSE,
                   "subform_table_show" => TRUE, 
                    'label' =>__('Marker'),
                    'urls' => array(
                        'data' => 'jx/admin/upload/highlitingtypologymarker',
                        'delete' => 'jx/admin/upload/highlitingtypologymarker?file=$1',
                        'delete_options' => array(
                           '$1' => 'marker',
                        ),
                        'download' => 'admin/download/highlitingtypologymarker/index/$1',
                        'download_options' => array(
                            '$1' => 'marker',
                            ),
                        'thumbnail' => 'admin/download/highlitingtypologymarker/thumbnail/$1',
                        'thumbnail_options' => array(
                            '$1' => 'marker',
                            ),
                    ),  
                ),
            );
      }
      
      
     
    
}
