<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Page extends ORM {
    
    
    
    public function labels() {
        return array(
            "alpha_id" => __("Alphanumeric ID"),
            "title" => __("Title"),
            "body" => __("Body"),
        );
    }
    
    public function rules()
    {
        return array(
            'title' => array(
                    array('not_empty'),
            ),
            'body' => array(
                    array('not_empty'),
            ),
             'alpha_id' => array(
                    array('not_empty'),
            ),
        );
    }

}