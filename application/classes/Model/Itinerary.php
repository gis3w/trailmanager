<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Itinerary extends ORM {
    
    
     protected $_has_many = array(
        'pois' => array(
            'model'   => 'Poi',
            'through' => 'itineraries_pois',
            'orm_type' => 'GIS',
        ),
        'paths' => array(
            'model'   => 'Path',
            'through' => 'itineraries_paths',
            'orm_type' => 'GIS',
        ),
        'areas' => array(
            'model'   => 'Area',
            'through' => 'itineraries_areas',
            'orm_type' => 'GIS',
        ),
         'images' => array(
            'model'   => 'Image_Itinerary',
        ),
         'urls' => array(
            'model'   => 'Url_Itinerary',
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