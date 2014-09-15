<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Search_Data extends ORM {
  
    public function getElement(){
        // we get  element on the base of type
        $ORMType = in_array($this->type, array('Poi','Path')) ?  'ORMGIS' : 'ORM';
       return $ORMType::factory($this->type,$this->id);
    }
}