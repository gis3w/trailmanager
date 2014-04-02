<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Path extends ORMGIS {
    
    public $geotype = ORMGIS::TP_MULTILINESTRING;
    
    public $epsg_db = 4326;
    public $epsg_out = 4326;
    
     protected $_has_many = array(
        'itineraries' => array(
            'model'   => 'Itinerary',
            'through' => 'itineraries_paths',
        ),
        'typologies' => array(
            'model'   => 'Typology',
            'through' => 'typologies_paths',
        ),
    );
    
    public function labels() {
        return array(
            "title" => __("Title"),
            "description" => __("Description"),
            "reason" => __("Resasons"),
            "accessibility" => __("Accessibility"),
            "information_url" => __("Information url"),
        );
    }
    
    
    public function rules()
    {
        return array(
            'title' => array(
                    array('not_empty'),
            ),
            'length' => array(
                    array('not_empty'),
                    array('numeric')
            ),
            'altitude_gap' => array(
                    array('not_empty'),
                    array('numeric')
            ),
            
        );
    }

}