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
            'fields' => array('id','title','description','length','altitude_gap','reason','general_features','accessibility','information_url'),
        ),
       array(
            'name' => 'path-foreign-data',
            'position' => 'right',
            'fields' => array('typologies','the_geom','color'),
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
                ),
                 "reason" => array(
                    'form_input_type' => self::TEXTAREA,
                ),
                 "accessibility" => array(
                    'form_input_type' => self::TEXTAREA,
                ),
                "general_features" => array(
                    'form_input_type' => self::TEXTAREA,
                ),
                "color" => array(
                    "form_input_type" => self::COLORPICKER,
                    "class" => "color-path",
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
            );
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
        
      
        return $fct;
        
    }
 
     
    
}
