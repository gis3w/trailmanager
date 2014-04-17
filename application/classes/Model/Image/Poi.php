<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Image_Poi extends ORM {
    
    protected $_belongs_to = array(
            'poi' => array( ),
        );
    
    public function labels() {
        return array(
            "Image" => __("Image"),
            "description" => __("Description"),
        );
    }
    
}