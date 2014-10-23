<?php defined('SYSPATH') OR die('No direct access allowed.');


return array(
    'main' => array(
		/*
        'map' => array(
            'id' =>'map',
            'name' => __('Map'),
            'url' => 'jx/home',
            'capability' => NULL,
            'icon' => 'globe',
        )
		*/
        'info' => array(
            'id' =>'info',
            'name' => __('Info'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'info-sign',
        ),
        'to_default_extent' => array(
            'id' =>'to_default_extent',
            'name' => __('Start extent'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'fullscreen',
        ),
        'itinerary' => array(
            'id' =>'itinerary',
            'name' => __('Itineraries'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'code-fork',
        ),
        'poi' => array(
            'id' =>'poi',
            'name' => __('Points of interest'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'map-marker',
        ),
        'path' => array(
            'id' =>'path',
            'name' => __('Paths'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'location-arrow',
        ),
         'area' => array(
            'id' =>'area',
            'name' => __('Areas of interest'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'crop',
        ),
    ),
    
);
