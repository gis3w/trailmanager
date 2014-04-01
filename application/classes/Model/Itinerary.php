<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Itinerary extends ORM {
    
    
     protected $_has_many = array(
        'pois' => array(
            'model'   => 'Poi',
            'through' => 'itineraries_pois',
        ),
        'paths' => array(
            'model'   => 'Path',
            'through' => 'itineraries_paths',
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
        );
    }

}