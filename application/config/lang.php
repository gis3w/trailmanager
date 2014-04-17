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
                'publish',
                'the_geom',
            )
        ),
        'paths' => array(
            'columns_to_exlude' => array(
                'typology_id',
                'publish',
                'the_geom',
            )
        ),
        'itineraries' => array(),
        'typologies' => array(),
        'image_pois' => array(
            'columns_to_exlude' => array(
                'file',
                'data_ins',
                'data_mod',
                'poi_id',
            )
        ),
        'image_paths' => array(
            'columns_to_exlude' => array(
                'file',
                'data_ins',
                'data_mod',
                'path_id',
            )
        ),
        'video_pois' => array(
            'columns_to_exlude' => array(
                'embed',
                'data_ins',
                'data_mod',
                'poi_id',
            )
        ),
        'video_paths' => array(
            'columns_to_exlude' => array(
                'embed',
                'data_ins',
                'data_mod',
                'poi_id',
            )
        ),
        'image_itineraries' => array(
            'columns_to_exlude' => array(
                'file',
                'data_ins',
                'data_mod',
                'path_id',
            )
        ),
    ),
    
);