<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Theme extends ORM {

    public function __toString() {
        return $this->name;
    }
}