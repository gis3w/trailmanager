<?php defined('SYSPATH') OR die('No direct access allowed.');


return array(
    'dropdown' =>array(
        'dropdown_mydata' => array(
            'id' => 'dropdown_mydata',
            'capability' => NULL,
            'name' => __('My data'),
            'icon' => 'user'
        ),
    ),
    'main' => array(
        'info' => array(
            'id' =>'info',
            'name' => __('Info'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'info-sign',
        ),
        'itinerary' => array(
            'id' =>'itinerary',
            'name' => __('Itineraries'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'code-fork',
        ),
        'addGeometries' => array(
            'id' =>'addGeometries',
            'name' => __('New Report'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'plus',
        ),
        'favorities' => array(
            'id' =>'favorities',
            'name' => __('Favorities'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'star',
            'dropdown' => 'dropdown_mydata',
        ),
    		'getroute' => array(
    				'id' =>'getroute',
    				'name' => __('Get route'),
    				'url' => NULL,
    				'capability' => NULL,
    				'icon' => 'road',
    		),
        'login' => array(
            'id' =>'login',
            'name' => __('Login'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'user',
            'display' => FALSE,
        ),
        'logout' => array(
            'id' =>'logout',
            'name' => __('Logout'),
            'url' => NULL,
            'capability' => NULL,
            'icon' => 'off',
            'display' => FALSE,
        ),
    ),
    
);
