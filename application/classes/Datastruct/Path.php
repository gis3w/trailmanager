<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Path extends Datastruct {
    
    protected $_nameORM = "Path";
    protected $_typeORM = "ORMGIS";
    
    public $icon = 'location-arrow';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'path-data',
            'position' => 'left',
            'fields' => array('id','title','publish','description','length','altitude_gap','reason','period_schedule','general_features','accessibility','information_url'),
        ),
       array(
            'name' => 'path-foreign-data',
            'position' => 'right',
            'fields' => array('typology_id','typologies','path_modes','the_geom','color','width','image_path','video_path'),
        ),
        array(
            'name' => 'path-block-data',
            'position' => 'block',
            'fields' => array('url_path'),
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
                "general_features" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
                "color" => array(
                    "form_input_type" => self::MAPBOX_COLOR,
                    "class" => "color-path",
                ),
                 "width" => array(
                    "default_value" => 3,
                ),
                 "period_schedule" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
                 "the_geom" => array(
                    'form_input_type' => self::MAPBOX,
                    'map_box_editing' => TRUE,
                    'map_box_editing_geotype' => array(
                        self::GEOTYPE_POLYLINE
                    ),
                    'map_box_fileloading' => TRUE,
                    'label' =>__('Geodata'),
                    'table_show' => FALSE,
                ),
                "information_url" => array(
                    'prefix' => 'http://'
                ),
                 "altitude_gap" => array(
                    'suffix' => 'm',
                ),
                "length" => array(
                    'suffix' => 'km',
                ),
                "typology_id" => array(
                    'form_input_type' => self::SELECT,
                    'foreign_mode' => self::SINGLESELECT,
                    'foreign_toshow' => '$1',
                    'foreign_toshow_params' => array(
                        '$1' => 'name',
                    ),
                    'url_values' => '/jx/typology',
                    'label' => __('Main typology'),
                     'description' => __('Select the main typology  for this point of interest'),
                     "table_show" => TRUE,
                )
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
                    'delete' => 'jx//admin/upload/image?file=$1',
                    'delete_options' => array(
                        '$1' => 'nome',
                    ),
                    'download' => 'admin/download/image/$1/$2',
                    'download_options' => array(
                        '$1' => 'path_id',
                        '$2' => 'nome',
                        ),
                ),
             )
        );
         
         $fct['image_path'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBFORM,
                "table_show" => FALSE,
                'foreign_mode' => self::MULTISELECT,
                'foreign_key' => 'path_id',
                'validation_url' => 'jx/admin/imagepath',
                'label' => __('Images to upload'),
             )
        );
         
        $fct['video_path'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBFORM,
                 'form_name' => 'video_path',
                "table_show" => FALSE,
                'foreign_mode' => self::MULTISELECT,
                'foreign_key' => 'path_id',
                'validation_url' => 'jx/admin/videopath',
                'label' => __('Videos to embed'),
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
        
        $fct['path_modes']  = array_replace($this->_columnStruct,array(
            'data_type' => 'integer',
            'form_input_type' => self::SELECT,
            'foreign_mode' => self::MULTISELECT,
            'foreign_key' => 'path_modes',
            'foreign_toshow' => '$1',
            'foreign_toshow_params' => array(
                '$1' => 'mode',
            ),
            'label' => __('Modes'),
             'description' => __('Select one or more modes for  this path'),
             "table_show" => FALSE,
        ));
        
        $fct['url_path'] = array_replace($this->_columnStruct,array(
             
            'data_type' => 'multifield',
            'label' => __('Urls path'),
            "table_show" => FALSE,
            
        ));
        
      
        return $fct;
        
    }
 
     
    
}
