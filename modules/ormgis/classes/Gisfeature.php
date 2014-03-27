<?php defined('SYSPATH') or die('No direct script access.');

class Gisfeature {

    public $asGeoJson;

    public function  __construct($feat=NULL) {

        if(isset($feat)){

            $this->asGeoJson = json_decode($feat);
            
        }

    }

}
