<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Administration_Pathmodes extends Datastruct {
    
    public $enctype = self::ECNTYPE_MULTIPART;
    
    protected $_nameORM = "Path_Mode";
    
     public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "mode",
            
        )
    );
    
    public $groups = array(
        array(
            'name' => 'Pathmodes-data',
            'position' => 'left',
            'fields' => array('id','mode','description','icon'),
        ),
    );
    
     protected function _columns_type() {
        
        return array(
            "description" => array(
                "form_input_type" => self::TEXTAREA
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
                        'data' => 'jx/admin/upload/pathmodeicon',
                        'delete' => 'jx/admin/upload/pathmodeicon?file=$1',
                        'delete_options' => array(
                           '$1' => 'icon',
                        ),
                        'download' => 'admin/download/pathmodeicon/index/$1',
                        'download_options' => array(
                            '$1' => 'icon',
                            ),
                        'thumbnail' => 'admin/download/pathmodeicon/thumbnail/$1',
                        'thumbnail_options' => array(
                            '$1' =>'icon',
                            ),
                    ),  
                ),
        );
    }  
    
}
  