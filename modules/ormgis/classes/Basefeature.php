<?php defined('SYSPATH') or die('No direct script access.');

 abstract class Basefeature {

     protected $filterProperties = array();
     
    public $type;
    
    
   
    public function as_geoJson(){
        return json_encode($this);
    }

}
