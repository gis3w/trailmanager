<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Poi extends ORMGIS {
    
    public $geotype = ORMGIS::TP_POINT;
    
    public $epsg_db = 4326;
    public $epsg_out = 4326;
    
    
    protected $_has_many = array(
        'itineraries' => array(
            'model'   => 'Itinerary',
            'through' => 'itineraries_pois',
        ),
        'typologies' => array(
            'model'   => 'Typology',
            'through' => 'typologies_pois',
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
        );
    }

}