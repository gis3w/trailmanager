<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Typology extends ORM {
    
    
     protected $_has_many = array(
        'pois' => array(
            'model'   => 'Poi',
            'through' => 'typologies_pois',
        ),
        'paths' => array(
            'model'   => 'Path',
            'through' => 'typologies_paths',
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