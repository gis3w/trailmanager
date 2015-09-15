<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Highliting_Path extends Model_Highliting{
    
    public $geotype = ORMGIS::TP_MULTILINESTRING;

    protected $_hightliting_type = 'Path';


    protected $_has_many = array(
        'images' => array(
            'model'   => 'Image_Highliting_Path',
        ),
        'states' => array(
            'model'   => 'State_Highliting_Path',
        ),
    );
    
    
}