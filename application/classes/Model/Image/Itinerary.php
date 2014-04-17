<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Image_Itinerary extends ORM {
    
    protected $_belongs_to = array(
            'itinerary' => array( ),
        );
    
    public function labels() {
        return array(
            "Image" => __("Image"),
            "description" => __("Description"),
        );
    }
    
}