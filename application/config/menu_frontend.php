<?php defined('SYSPATH') OR die('No direct access allowed.');


return array(
    'main' => array(
        'map' => array(
            'id' =>'map',
            'name' => __('Map'),
            'url' => 'jx/home',
            'capability' => NULL,
            'icon' => 'globe',
        ),
         'locateButton' => array(
            'id' =>'locateButton',
            'name' => __('Localize me'),
            'url' => '#',
            'capability' => NULL,
            'icon' => 'globe',
        ),
    ),
    
);
