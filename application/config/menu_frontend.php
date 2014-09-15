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
            'icon' => NULL,
        ),
        'itinerary' => array(
            'id' =>'itinerary',
            'name' => __('Itineraries'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'code-fork',
        ),
        'path' => array(
            'id' =>'path',
            'name' => __('Paths'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'location-arrow',
        ),
        'poi' => array(
            'id' =>'poi',
            'name' => __('Points of interest'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'map-marker',
        ),
    ),
    
);
