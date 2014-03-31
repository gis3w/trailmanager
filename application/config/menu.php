<?php defined('SYSPATH') OR die('No direct access allowed.');

//vaci singole o subgroup con items
// tipi di subgroup: tabs, affix, dropdowntab,collapse

return array(
    'main' => array(
        'home' => array(
            'id' =>'home',
            'name' => __('Home'),
            'url' => 'jx/home',
            'url_mobile' => 'home',
            'capability' => NULL,
            'icon' => 'home',
        ),
         'itinerary' => array(
            'id' =>'itinerary',
            'name' => __('Itineraries'),
            'url' => '/jx/admin/itinerary',
            'url_mobile' => 'user',
             'capability' => 'access-user',
            'icon' => 'user',
        ),
        'user' => array(
            'id' =>'user',
            'name' => __('Users'),
            'url' => '/jx/user',
            'url_mobile' => 'user',
            'capability' => 'access-user',
            'icon' => 'user',
        ),
        
        'administration' => array(
            'id' =>'administration',
            'name' => __('Administration'),
            'capability' => 'access-administration',
            'icon' => 'cog',
            'menu' => array(
                'type' => 'tabs',
                'items' => array(
                    'administration_capabilities' => array(
                        'id' =>'capabilities',
                        'name' => __('ACL'),
                        'url' => '/jx/administration/capabilities',
                        'capability' => 'access-administration-capabilities',
                        'icon' => 'user',
                    ),
                    'administration_roles' => array(
                        'id' =>'roles',
                        'name' => __('Roles'),
                        'url' => '/jx/administration/roles',
                        'capability' => 'access-administration-roles',
                        'icon' => 'user',
                    ),
             ),
            ),
            
        ),
        'logout' => array(
            'id' =>'logout',
            'name' => __('Logout'),
            'url' => '/login/out',
            'url_mobile' => 'login/out',
            'capability' => NULL,
            'icon' => 'off',
        ),
        
    ),

);
