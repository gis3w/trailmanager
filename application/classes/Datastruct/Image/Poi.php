<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Image_Poi extends Datastruct {
    
    public $enctype = self::ECNTYPE_MULTIPART;
    
    protected $_nameORM = "Image_Poi";

    public static $preKeyField = 'imagepoi';
    
    public $icon = 'suitcase';
    public $filter = TRUE;
    
    public $sortable = TRUE;

    public $groups = array(
        array(
            'name' => 'poi-data',
            'position' => 'left',
            'fields' => array('id','file','description'),
        ),
       
    );
    
    public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "file"
        )
    );
    
     protected function _columns_type() {
        
            return array(
                "id" => array(
                    'form_input_type' => self::HIDDEN,
                    "subform_table_show" => FALSE, 
                ),
                "description" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
                "file" => array(
                   "form_input_type" => self::INPUT,
                    "multiple" => FALSE,
                    "data_type" => self::FILE,
                    "form_show" => TRUE,
                    "table_show" => FALSE,
                   "subform_table_show" => TRUE, 
                    'label' =>__('Image'),
                    'urls' => array(
                        'data' => 'jx/admin/upload/imagepoi',
                        'delete' => 'jx/admin/upload/imagepoi?file=$1',
                        'delete_options' => array(
                            '$1' => 'nome',
                        ),
                        'download' => 'admin/download/imagepoi/$1/$2',
                        'download_options' => array(
                            '$1' => 'poi_id',
                            '$2' => 'nome',
                            ),
                    ),  
                ),
                 "poi_id" => array(
                    'form_input_type' => self::HIDDEN,
                    "subform_table_show" => FALSE,
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
