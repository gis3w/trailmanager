<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Highliting_Poi extends Model_Highliting{
    
    public $geotype = ORMGIS::TP_POINT;

    protected $_hightliting_type = 'Point';


    protected $_has_many = array(
        'images' => array(
            'model'   => 'Image_Highliting_Poi',
        ),
        'states' => array(
            'model'   => 'State_Poi',
        ),
    );
    
    
}