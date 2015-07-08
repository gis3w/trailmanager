<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Anonimous_Highlitings_Data extends ORM {


    public function rules()
    {
        return array(
            'name' => array(
                    array('not_empty'),
            ),
            'surname' =>array(
                    array('not_empty'),
            ),
            'email' =>array(
                    array('not_empty'),
            ),
            
        );
    }
    
    public function labels() {
        return array(
            "name" => __("Name"),
            "surname" => __("Surname"),
        );
    }
}