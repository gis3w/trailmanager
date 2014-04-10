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
             'capability' => 'access-admin-itinerary',
            'icon' => 'code-fork',
        ),
         'poi' => array(
            'id' =>'poi',
            'name' => __('Points of interest'),
            'url' => '/jx/admin/poi',
            'url_mobile' => 'user',
             'capability' => 'access-admin-poi',
            'icon' => 'map-marker',
        ),
        'path' => array(
            'id' =>'path',
            'name' => __('Paths'),
            'url' => '/jx/admin/path',
            'url_mobile' => 'user',
             'capability' => 'access-admin-poi',
            'icon' => 'location-arrow',
        ),
        'user' => array(
            'id' =>'user',
            'name' => __('Users'),
            'url' => '/jx/user',
            'url_mobile' => 'user',
            'capability' => 'access-admin-user',
            'icon' => 'user',
        ),
         'global' => array(
            'id' =>'global',
            'name' => __('Global'),
            'capability' => 'access-admin-global',
            'icon' => 'cog',
             'menu' => array(
                 'type' => 'tabs',
                 'items' => array(
                      'tipologies' => array(
                         'name' => __('Tipologies'),
                         'icon' =>'suitcase',
                         'menu' => array(
                             'type' => 'affix',
                             'items' => array(
                                'global_typology' => array(
                                      'id' =>'typology',
                                      'name' => __('Typologies'),
                                      'url' => '/jx/admin/global/typology',
                                      'capability' => 'access-global-typology',
                                      'icon' => 'list-alt',
                               ),
                            )
                         )
                      )
                 )
            )
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
                        'url' => '/jx/admin/administration/capabilities',
                        'capability' => 'access-administration-capabilities',
                        'icon' => 'user',
                    ),
                    'administration_roles' => array(
                        'id' =>'roles',
                        'name' => __('Roles'),
                        'url' => '/jx/admin/administration/roles',
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
