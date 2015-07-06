<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Area extends ORMGIS {
    
    public $geotype = ORMGIS::TP_MULTIPOLYGON;
    
    public $epsg_db = 4326;
    public $epsg_out = 4326;
    
     protected $_has_many = array(
        'itineraries' => array(
            'model'   => 'Itinerary',
            'through' => 'itineraries_areas',
        ),
        'typologies' => array(
            'model'   => 'Typology',
            'through' => 'typologies_areas',
        ),
         'images' => array(
            'model'   => 'Image_Area',
        ),
          'videos' => array(
            'model'   => 'Video_Area',
        ),
          'urls' => array(
            'model'   => 'Url_Area',
        ),
    );
    
    public function labels() {
        return array(
            "title" => __("Title"),
            "description" => __("Description"),
            "plus_information" => __("More informations"),
            "publish" => __("Published"),
            "typology_id" => __("Main typology"),
            "color" => __("Color"),
            "width" => __("Width"),
            "inquiry" => __('Request informations'),
            "accessibility" => __("Accessibility"),
            "period_schedule" => __("Period schedule"),
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
            'the_geom' =>array(
                array('not_empty'),
            ),
            
        );
    }

}