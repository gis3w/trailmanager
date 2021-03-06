<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Image_Highliting_Poi extends ORM {
    
    protected $_belongs_to = array(
            'poi' => array( ),
        );
    
    public function labels() {
        return array(
            "file" => __("Image"),
            "description" => __("Description"),
        );
    }

    public function rules()
    {
        return array(
            'file' => array(
                    array('not_empty'),
            ),
        );
    }
    
}