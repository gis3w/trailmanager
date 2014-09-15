<?php defined('SYSPATH') OR die('No direct access allowed.');

//vaci singole o subgroup con items
// tipi di subgroup: tabs, affix, dropdowntab,collapse

return array(
     'dropdown' =>array(
         'dropdown_administration' => array(
            'id' => 'dropdown_administration',
            'capability' => 'access-administration',
            'name' => __('Global/Admin'),
            'icon' => 'cog'
        )
    ),
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
             'capability' => 'access-admin-path',
            'icon' => 'location-arrow',
        ),
        'area' => array(
            'id' =>'area',
            'name' => __('Areas'),
            'url' => '/jx/admin/area',
            'url_mobile' => 'user',
             'capability' => 'access-admin-area',
            'icon' => 'crop',
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
             'dropdown' => 'dropdown_administration',
             'menu' => array(
                 'type' => 'tabs',
                 'items' => array(
                      'typologies' => array(
                         'name' => __('Typologies'),
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
            'dropdown' => 'dropdown_administration',
            'menu' => array(
                'type' => 'tabs',
                'items' => array(
                     'administration_pathmodes' => array(
                        'id' =>'pathmodes',
                        'name' => __('Path modes'),
                        'url' => '/jx/admin/administration/pathmodes',
                        'capability' => 'access-administration-pathmodes',
                        'icon' => 'cog',
                    ),
                    'administration_globalconfigs' => array(
                        'id' =>'globalconfigs',
                        'name' => __('Global configs'),
                        'url' => '/jx/admin/administration/globalconfigs',
                        'capability' => 'access-administration-globalconfigs',
                        'icon' => 'cog',
                    ),
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
                    'administration_backgroundlayers' => array(
                        'id' =>'backgroundlayers',
                        'name' => __('Background layers'),
                        'url' => '/jx/admin/administration/backgroundlayers',
                        'capability' => 'access-administration-backgroundlayers',
                        'icon' => 'leaf',
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
