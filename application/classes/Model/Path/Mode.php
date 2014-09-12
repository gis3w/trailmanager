<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Path_Mode extends ORM {
    
    
     protected $_has_many = array(
        'paths' => array(
            'model'   => 'Path',
            'through' => 'path_modes_paths',
        ),
    );
    
    public function labels() {
        return array(
            "mode" => __("Mode"),
            "description" => __("Description"),
        );
    }
    
    public function rules()
    {
        return array(
            'mode' => array(
                    array('not_empty'),
            ),
        );
    }

}