<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Path extends Datastruct {
    
    protected $_nameORM = "Path";
    
    public $icon = 'location-arrow';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'path-data',
            'position' => 'left',
            'fields' => array('id','title','description','lenght','altitude_gap','general_features'),
        ),
       array(
            'name' => 'path-foreign-data',
            'position' => 'right',
            'fields' => array('typologies'),
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
