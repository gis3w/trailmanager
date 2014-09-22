<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Administration_Backgroundlayers extends Datastruct {
    
    protected $_nameORM = "Background_Layer";
    
     public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "name",
            
        )
    );
    
    public $groups = array(
        array(
            'name' => 'backgroundlayers-data',
            'position' => 'left',
            'fields' => array('id','sections','layer_type_id','name','description','url','layers','styles','version','format','transparent','crs','def','source'),
        ),
    );
    
     protected function _columns_type() {
        
        return array(
            "description" => array(
                "form_input_type" => self::TEXTAREA
            ),
             "layer_type_id" => array(
                    'form_input_type' => self::SELECT,
                    'foreign_mode' => self::SINGLESELECT,
                    'foreign_toshow' => '$1',
                    'foreign_toshow_params' => array(
                        '$1' => 'type',
                    ),
                    'url_values' => '/jx/admin/layer_type',
                     'description' => __('Select the main typology '),
                     "table_show" => TRUE,
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
  