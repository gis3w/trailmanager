<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Poi extends Datastruct {
    
    protected $_nameORM = "Poi";
    protected $_typeORM = "ORMGIS";


    public $icon = 'suitcase';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'poi-data',
            'position' => 'left',
            'fields' => array('id','publish','title','description','reason','period_schedule','accessibility','inquiry','pdf_print_qrcode'),
        ),
       array(
            'name' => 'poi-foreign-data',
            'position' => 'right',
            'fields' => array('typology_id','typologies','the_geom','max_scale','image_poi','video_poi'),
        ),
        array(
            'name' => 'poi-block-data',
            'position' => 'block',
            'fields' => array('url_poi'),
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
                 "reason" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
                 "accessibility" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
                "period_schedule" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
                "the_geom" => array(
                    'form_input_type' => self::MAPBOX,
                    'map_box_editing' => TRUE,
                    'map_box_editing_geotype' => array(
                        self::GEOTYPE_MARKER
                    ),
                    'map_box_fileloading' => TRUE,
                    'label' =>__('Geodata'),
                    'table_show' => FALSE,
                ),
                "typology_id" => array(
                    'form_input_type' => self::SELECT,
                    'foreign_mode' => self::SINGLESELECT,
                    'foreign_toshow' => '$1',
                    'foreign_toshow_params' => array(
                        '$1' => 'name',
                    ),
                    'url_values' => '/jx/typology',
                     'description' => __('Select the main typology  for this point of interest'),
                     "table_show" => TRUE,
                ),
                "inquiry" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
                "max_scale" => array(
                    'prefix' => '1:',
                    'table_show' => FALSE,
                ),
            );
      }
      
       protected function _extra_columns_type()
    {
        $fct = array();

         $fct['images'] = array_replace($this->_columnStruct, array(
                "form_input_type" => self::INPUT,
                "multiple" => FALSE,
                "data_type" => 'jquery_fileupload',
                "form_show" => TRUE,
                "table_show" => FALSE,
               "subform_table_show" => TRUE, 
                'label' =>__('Images to upload'),
                'urls' => array(
                    'data' => 'jx/admin/upload/image',
                    'delete' => 'jx/admin/upload/image?file=$1',
                    'delete_options' => array(
                        '$1' => 'nome',
                    ),
                    'download' => 'admin/download/image/$1/$2',
                    'download_options' => array(
                        '$1' => 'poi_id',
                        '$2' => 'nome',
                        ),
                ),
             )
        );
         
         $fct['image_poi'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBFORM,
                "table_show" => FALSE,
                'foreign_mode' => self::MULTISELECT,
                'foreign_key' => 'poi_id',
                'validation_url' => 'jx/admin/imagepoi',
                'label' => __('Images to upload'),
             )
        );
         
         
        $fct['video_poi'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBFORM,
                "table_show" => FALSE,
                'foreign_mode' => self::MULTISELECT,
                'foreign_key' => 'poi_id',
                'validation_url' => 'jx/admin/videopoi',
                'label' => __('Videos to embed'),
             )
        );
        
        $fct['pdf_print_qrcode']  = array_replace($this->_columnStruct,array(
                    'form_input_type' => self::BUTTON,
                    'input_class' => 'default',
                    'data_type' => 'pdf_print',
                    'url_values' => '/print/report/company/global/$1',
                    'url_values_params' => array(
                        '$1' => 'id',
                    ),
                    'description' => __('Download qrcode position'),
                    'table_show' => FALSE,
                    'label' => __('Download qrcode'),
                    'icon' => 'download-alt',
                    'form_show' => array(
                        self::STATE_INSERT => FALSE,
                        self::STATE_UPDATE =>TRUE
                    ),
                )
        );
        
        return $fct;
        
    }


      protected function _foreign_column_type() {
                
        $fct['typologies']  = array_replace($this->_columnStruct,array(
            'data_type' => 'integer',
            'form_input_type' => self::SELECT,
            'foreign_mode' => self::MULTISELECT,
            'foreign_toshow' => '$1',
            'foreign_toshow_params' => array(
                '$1' => 'name',
            ),
            'url_values' => '/jx/typology',
            'label' => __('Typologies'),
             'description' => __('Select one or more typology  for this point of interest'),
             "table_show" => FALSE,
        ));
        
        $fct['url_poi'] = array_replace($this->_columnStruct,array(
             
            'data_type' => 'multifield',
            'label' => __('Urls poi'),
            "table_show" => FALSE,
            
        ));
        
      
        return $fct;
        
    }
     
    
}
