<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Heights_Profile_Path extends ORM implements JsonSerializable {

    protected $_belongs_to = array(
        'path' => array( ),
    );

    public function JsonSerialize(){
        return $this->as_array();
    }
}