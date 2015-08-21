<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'mappath' => MODPATH.'print/mapserver/',
    'mapfile' => 'print.map',
    'tmp_dir' => APPPATH.'../public/map/',
    'image_base_url' => '/public/map/',
    'pdf_map_size' => [
        'A4' => [
            'P' => [
                'width' => 740,
                'height' => 770,
            ],
            'L' => [
                'width' => 1080,
                'height' => 540
            ]
        ]
    ],
    'mapping_trailmanager_mapproxy' => [

    ]
    );
