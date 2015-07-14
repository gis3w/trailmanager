<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Anonimous_Highlitings_Data extends Datastruct {
    
    
     public $title = array(
        "title_toshow" => "$1 $2",
        "title_toshow_params" => array(
            "$1" => "name",
            "$2" => "surname",
        )
    );
     public $groups = array(
         array(
            'name' => 'anonimous_highlitings_data-data',
            'position' => 'left',
            'fields' => array('name','surname','email','comune','frazione','via'),
        ),
    );
     
    protected function _columns_type() {
       
        return array(
                    "poi_id" => array(
                        'form_show' => FALSE,
                        'table_show' => FALSE,
                    ),
                    "path_id" => array(
                        'form_show' => FALSE,
                        'table_show' => FALSE,
                    ),
                    "area_id" => array(
                        'form_show' => FALSE,
                        'table_show' => FALSE,
                    ),
            );
    }
}

