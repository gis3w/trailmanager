<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Video_Poi extends ORM {
    
    protected $_belongs_to = array(
            'poi' => array( ),
        );
    
    public function labels() {
        return array(
            "title" => __("Title"),
            "description" => __("Description"),
            "embed" => __("Video incorporato"),
        );
    }
    
    public function rules()
    {
        return array(
            'title' => array(
                    array('not_empty'),
            ),
            'embed' =>array(
                    array('not_empty'),
            ),
        );
    }
    
}