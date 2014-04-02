<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Poi extends Datastruct {
    
    protected $_nameORM = "Poi";
    
    public $icon = 'suitcase';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'poi-data',
            'position' => 'left',
            'fields' => array('id','title','description'),
        ),
       array(
            'name' => 'poi-foreign-data',
            'position' => 'right',
            'fields' => array('typologies','the_geom'),
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
                "the_geom" => array(
                    'form_input_type' => self::MAPBOX,
                    'map_box_editing' => TRUE,
                    'map_box_editing_geotype' => array(
                        'polyline',
                        'polygon',
                        'marker',
                    ),
                    'map_box_fileloading' => TRUE,
                    'label' =>__('Geodata'),
                    'description' => __('I geodati che possono essere inviati al veicolo'),
                    'table_show' => FALSE,
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
