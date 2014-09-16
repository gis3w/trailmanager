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
        'images' => array(
            'model'   => 'Image_Poi',
        ),
         'videos' => array(
            'model'   => 'Video_Poi',
        ),
        'urls' => array(
            'model'   => 'Url_Poi',
        ),
    );
    
    public function labels() {
        return array(
            "title" => __("Title"),
            "description" => __("Description"),
            "reason" => __("Reasons"),
            "accessibility" => __("Accessibility"),
            "information_url" => __("Information url"),
            "publish" => __("Published"),
            "typology_id" => __("Main typology"),
            "period_schedule" => __("Period schedule"),
            "inquiry" => __('Request informations'),
        );
    }
    
    
    public function rules()
    {
        return array(
            'title' => array(
                    array('not_empty'),
            ),
            'publish' =>array(
                    array('not_empty'),
            ),
            'typology_id' =>array(
                    array('not_empty'),
            ),
        );
    }

}