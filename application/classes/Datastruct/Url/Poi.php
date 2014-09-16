<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Url_Poi extends Datastruct {
    
    protected $_nameORM = 'Url_Poi';

    
    protected function _columns_type() {
        
            return array(
                "id" => array(
                     'form_show' => FALSE,
                    'editable' => FALSE,
                     "table_col_width_%" => 10 
                ),
                 "poi_id" => array(
                    'unset' => TRUE,
                     'subform_table_show' => FALSE,
                ),
                "url" => array(
                    'prefix' => 'http://',
                     "table_col_width_%" => 33
                ),
            );
      }

   
}