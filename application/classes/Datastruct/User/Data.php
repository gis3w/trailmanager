<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_User_Data extends Datastruct {
    
     protected function _columns_type() {
        
        return array(
            "data_nascita" => array(
                'form_input_type' => self::DATE,
            ),
        );
     }
}

