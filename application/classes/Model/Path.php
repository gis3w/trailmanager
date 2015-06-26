<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Path extends ORMGIS {
    
    public $geotype = ORMGIS::TP_MULTILINESTRING;
    
    public $epsg_db = 3004;
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
         'modes' => array(
            'model'   => 'Path_Mode',
            'through' => 'path_modes_paths',
            'far_key' => 'path_mode_id'
        ),
         'urls' => array(
            'model'   => 'Url_Path',
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
            "typology_id" => __("Main typology"),
            "period_schedule" => __("Period schedule"),
            "color" => __("Color"),
            "width" => __("Width"),
            "inquiry" => __('Request informations'),
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
            'typology_id' =>array(
                    array('not_empty'),
            ),
            
        );
    }
    
     public function filters()
    {
        return array(
          
            'length' => array(
                    array('Filter::comma2point')
            ),
            'altitude_gap' => array(
                    array('Filter::comma2point')
            ),
            
            
        );
    }
    
    public function get($column) {
        
        switch($column)
        {
            case "length":
            case "altitude_gap":
                $value = Filter::point2comma((string)parent::get($column));
            break;

            case "considerable_points":
                $value = ORMGIS::factory('Considerable_Point')->where('se','=',$this->se);
            break;
        
            default:
                $value = parent::get($column);
        }
        return $value;
        
    }

}