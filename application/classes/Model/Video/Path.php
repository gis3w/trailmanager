<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Video_Path extends ORM {
    
     protected $_belongs_to = array(
            'path' => array( ),
        );
     
     public function labels() {
        return array(
            "title" => __("Title"),
            "description" => __("Description"),
            "embed" => __("Video incorporato"),
        );
    }
}