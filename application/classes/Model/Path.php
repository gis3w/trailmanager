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
         'images' => array(
            'model'   => 'Image_Path',
        ),
          'videos' => array(
            'model'   => 'Video_Path',
        ),
    );
    
    public function labels() {
        return array(
            "title" => __("Title"),
            "description" => __("Description"),
            "altitude_gap" => __("Altitude gap"),
            "general_features" => __("General features"),
            "accessibility" => __("Accessibility"),
            "reason" => __("Reasons"),
            "length" =>__("Length"),
            "accessibility" => __("Accessibility"),
            "information_url" => __("Information url"),
            "publish" => __("Published"),
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
            'publish' =>array(
                    array('not_empty'),
            ),
            
        );
    }

}