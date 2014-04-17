<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Image_Path extends ORM {
    
     protected $_belongs_to = array(
            'path' => array( ),
        );
     
     public function labels() {
        return array(
            "Image" => __("Image"),
            "description" => __("Description"),
        );
    }
}