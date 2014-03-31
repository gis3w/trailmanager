<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Itinerary extends Datastruct {
    
    protected $_nameORM = "Itinerary";
    
    public $icon = 'suitcase';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'itinerary-data',
            'position' => 'left',
            'fields' => array('id','name','description'),
        ),
       
    );
    
    public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "name"
        )
    );


 
     
    
}
