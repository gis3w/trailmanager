<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Itinerary extends Datastruct {
    
    protected $_nameORM = "Itinerary";
    
    public $icon = 'suitcase';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'itinerary-data',
            'position' => 'left',
            'fields' => array('id','name','description'),
        ),
        array(
            'name' => 'itinerary-foreign-data',
            'position' => 'right',
            'fields' => array('pois','paths'),
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
            'url_values' => '/jx/admin/poi',
            'label' => __('Points of interest'),
             'description' => __('Select one or more points of interest for this itinerary'),
             "table_show" => FALSE,
        ));
        
        $fct['paths']  = array_replace($fct['pois'],array(
            'url_values' => '/jx/admin/path',
            'label' => __('Paths'),
             'description' => __('Select one or more paths  for this itinerary'),
        ));
        
      
       

        return $fct;
        
    }


 
     
    
}
