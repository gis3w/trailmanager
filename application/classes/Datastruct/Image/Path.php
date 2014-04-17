<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Image_Path extends Datastruct_Image_Poi {
    
    protected $_nameORM = "Image_Path";

    protected function _columns_type() {
        $cls = parent::_columns_type();
        
               $cls["path_id"] = array_merge($cls["poi_id"],array(
                    'form_input_type' => self::HIDDEN,
                    "subform_table_show" => FALSE,
                )
            );
               
         return $cls;
      }             
  
}
