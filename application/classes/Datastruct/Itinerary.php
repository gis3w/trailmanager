<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Itinerary extends Datastruct {
    
    protected $_nameORM = "Itinerary";

    public $filter = FALSE;

    public $groups = array(
        array(
            'name' => 'itinerary-data',
            'position' => 'left',
            'fields' => array('id','name','description'),
        ),
        array(
            'name' => 'itinerary-foreign-data',
            'position' => 'right',
            'fields' => array('pois','paths','areas','the_geom','image_itinerary'),
        ),
       array(
            'name' => 'itinerary-block-data',
            'position' => 'block',
            'fields' => array('url_itinerary'),
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
                    'editor' => TRUE,
                ),
            );
      }
      
      protected function _foreign_column_type() {
            
                
        $fct['pois']  = array_replace($this->_columnStruct,array(
            'data_type' => 'integer',
            'form_input_type' => self::SELECT,
            'foreign_mode' => self::MULTISELECT,
            'foreign_toshow' => '$1',
            'foreign_toshow_params' => array(
                '$1' => 'title',
            ),
            'change' => '/jx/admin/change/itinerarymap?poi_id=',
            'url_values' => '/jx/admin/poi',
            'label' => __('Points of interest'),
             'description' => __('Select one or more points of interest for this itinerary'),
             "table_show" => FALSE,
        ));
        
        $fct['paths']  = array_replace($fct['pois'],array(
            'url_values' => '/jx/admin/path',
            'label' => __('Paths'),
            'change' => '/jx/admin/change/itinerarymap?path_id=',
             'description' => __('Select one or more paths  for this itinerary'),
        ));
        
         $fct['areas']  = array_replace($fct['pois'],array(
            'url_values' => '/jx/admin/area',
            'label' => __('Areas'),
            'change' => '/jx/admin/change/itinerarymap?area_id=',
             'description' => __('Select one or more areas  for this itinerary'),
        ));
        
        $fct['image_itinerary'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBFORM,
                "table_show" => FALSE,
                'foreign_mode' => self::MULTISELECT,    
                'foreign_key' => 'itinerary_id',
                'validation_url' => 'jx/admin/imageitinerary',
                'label' => __('Images to upload'),
             )
        );
        
       $fct['url_itinerary'] = array_replace($this->_columnStruct,array(
             
            'data_type' => 'multifield',
            'label' => __('Urls itineraries'),
            "table_show" => FALSE,
            
        ));
       
        return $fct;
        
    }
    
    protected function _extra_columns_type() {
        
        $ext = array();
        $ext["the_geom"] = array_replace($this->_columnStruct,array(
                    'form_input_type' => self::MAPBOX,
                    'map_box_editing' => FALSE,
                    'map_box_fileloading' => FALSE,
                    'label' =>__('Geodata'),
                    'table_show' => FALSE,
                ));
        
        return $ext;
    }


 
     
    
}
