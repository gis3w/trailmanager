<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Highliting_Typology extends ORM {
    
    
     protected $_has_many = array(
        'highlitingpois' => array(
            'model'   => 'Highliting_Poi',
        ),
    );
    
    public function labels() {
        return array(
            "name" => __("Name"),
            "description" => __("Description"),
        );
    }
    
    public function rules()
    {
        return array(
            'name' => array(
                    array('not_empty'),
            ),
            'icon' => array(
                    array('not_empty'),
            ),
        );
    }

}