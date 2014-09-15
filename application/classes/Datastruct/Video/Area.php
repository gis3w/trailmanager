<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Video_Area extends Datastruct_Video_Poi {
    
    protected $_nameORM = "Video_Area";
    
    public static $preKeyField = 'videoarea';

    protected function _columns_type() {
        $cls = parent::_columns_type();
        
               $cls["area_id"] = array_merge($cls["poi_id"],array(
                    'form_input_type' => self::HIDDEN,
                    "subform_table_show" => FALSE,
                )
            );
               
         return $cls;
      }             
  
}
