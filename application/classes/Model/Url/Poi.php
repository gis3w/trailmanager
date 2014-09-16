<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Url_Poi extends ORM {

    public function labels() {
        return array(
            "url" => __("Url"),
            "alias" => __("Alias"),
            "description_url" => __("Description url")
        );
    }
    
     public function rules()
    {
        return array(
            'url' => array(
                    array('not_empty'),
            ),
        );
    }


}