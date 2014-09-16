<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Url_Path extends Datastruct_Url_Poi {
    
    protected $_nameORM = 'Url_Path';

    
    protected function _columns_type() {
        
            return array_replace(parent::_columns_type(),array(
                 "path_id" => array(
                    'unset' => TRUE,
                    'subform_table_show' => FALSE,
                ),
            ));
      }

   
}