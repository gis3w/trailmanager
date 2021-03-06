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
            'fields' => array('id','name','description','sections','icon'),
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
            );
      }


    protected function _foreign_column_type() {

        $fct['sections']  = array_replace($this->_columnStruct,array(
            'data_type' => 'integer',
            'form_input_type' => self::SELECT,
            'foreign_mode' => self::MULTISELECT,
            'foreign_toshow' => '$1',
            'foreign_toshow_params' => array(
                '$1' => 'section',
            ),
            'url_values' => '/jx/admin/section',
            'label' => __('Sections'),
            "table_show" => TRUE,
        ));


        return $fct;

    }
    
}
