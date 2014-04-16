<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(

    'langs'=> array(
        'it' => 'Italiano',
        'en' => 'English'),
    'default' => 'it',
    'tables_to_translate' => array(
        'pois' => array(
            'columns_to_exlude' => array(
                'typology_id',
            )
        ),
        'paths' => array(
            'columns_to_exlude' => array(
                'typology_id',
            )
        ),
        'itineraries' => array(),
        'typologies' => array(),
    ),
    
);