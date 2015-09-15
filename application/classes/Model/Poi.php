<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Poi extends ORMGIS {
    
    public $geotype = ORMGIS::TP_POINT;
    
    public $epsg_db = 3004;
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
            "publish" => __("Published"),
            "typology_id" => __("Main typology"),
            "inquiry" => __('Request informations'),
            "max_scale" => __('Max scale'),
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
             'max_scale' =>array(
                    array('numeric'),
            ),
            'the_geom' =>array(
                array('not_empty'),
            ),
        );
    }

    public function get($column) {

        switch($column)
        {

            case "paths":
                $value = ORMGIS::factory('Path')->where('se','=',$this->se);
                break;

            default:
                $value = parent::get($column);

        }
        return $value;

    }

}