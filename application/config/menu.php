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
        ),
     'dropdown_trail_elements' => array(
             'id' => 'dropdown_trail_elements',
             'capability' => 'access-admin-poi',
             'name' => __('Trail elements'),
             'icon' => 'plus'
         ),
     'dropdown_highliting' => array(
             'id' => 'dropdown_highliting',
             'capability' => 'access-highliting',
             'name' => __('Highlitings'),
             'icon' => 'plus'
         ),
     'dropdown_user' => array(
             'id' => 'dropdown_user',
             'capability' => 'access-administration',
             'name' => __('Users'),
             'icon' => 'group'
         ),
    ),
    'main' => array(
        'home' => array(
            'id' =>'home',
            'name' => '',
            'url' => 'jx/home',
            'capability' => NULL,
            'icon' => 'home',
        ),
        'highliting_poi' => array(
            'id' =>'highliting_poi',
            'name' => __('Highliting point'),
            'url' => '/jx/admin/highlitingpoi',
            'capability' => 'access-admin-highlitingpoi',
            'dropdown' => 'dropdown_highliting',
            'icon' => 'map-marker',
        ),
         'itinerary' => array(
            'id' =>'itinerary',
            'name' => __('Itineraries'),
            'url' => '/jx/admin/itinerary',
            'url_mobile' => 'user',
            'capability' => 'access-admin-itinerary',
            'dropdown' => 'dropdown_trail_elements',
            'icon' => 'code-fork',
        ),
         'poi' => array(
            'id' =>'poi',
            'name' => __('Points of interest'),
            'dropdown' => 'dropdown_trail_elements',
            'url' => '/jx/admin/poi',
            'url_mobile' => 'user',
             'capability' => 'access-admin-poi',
            'icon' => 'map-marker',
        ),
        'path' => array(
            'id' =>'path',
            'name' => __('Paths'),
            'dropdown' => 'dropdown_trail_elements',
            'url' => '/jx/admin/path',
            'url_mobile' => 'user',
             'capability' => 'access-admin-path',
            'icon' => 'location-arrow',
        ),
        'path_segment' => array(
            'id' =>'path_segment',
            'name' => __('Path segments'),
            'dropdown' => 'dropdown_trail_elements',
            'url' => '/jx/admin/pathsegment',
            'url_mobile' => 'user',
            'capability' => 'access-admin-pathsegment',
            'icon' => 'location-arrow',
        ),
        'area' => array(
            'id' =>'area',
            'name' => __('Areas'),
            'dropdown' => 'dropdown_trail_elements',
            'url' => '/jx/admin/area',
            'url_mobile' => 'user',
             'capability' => 'access-admin-area',
            'icon' => 'crop',
        ),
        'user' => array(
            'id' =>'user',
            'name' => __('System users'),
            'url' => '/jx/user',
            'url_mobile' => 'user',
            'capability' => 'access-admin-user',
            'icon' => 'user',
            'dropdown' => 'dropdown_user',
        ),
        'anonimous_highlitings_data' => array(
            'id' =>'anonimous_highlitings_data',
            'name' => __('Anonimous Users'),
            'url' => SAFE::setBaseUrl('jx/admin/anonimoushighlitingsdata'),
            'capability' => 'access-admin-user',
            'icon' => 'user',
            'dropdown' => 'dropdown_user',
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
                         'icon' =>'th-list',
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
                      ),
                     'highlitingtypologies' => array(
                         'name' => __('Highliting typologies'),
                         'icon' =>'th-list',
                         'menu' => array(
                             'type' => 'affix',
                             'items' => array(
                                 'global_highlitingtypology' => array(
                                     'id' =>'highlitingtypology',
                                     'name' => __('Highliting typologies'),
                                     'url' => '/jx/admin/global/highlitingtypology',
                                     'capability' => 'access-global-typology',
                                     'icon' => 'list-alt',
                                 ),
                             )
                         )
                     ),
                     'pages' => array(
                         'name' => __('Pages'),
                         'icon' =>'book',
                         'menu' => array(
                             'type' => 'affix',
                             'items' => array(
                                'global_page' => array(
                                                'id' =>'page',
                                                'name' => __('Pages'),
                                                'url' => '/jx/admin/global/page',
                                                'capability' => 'access-global-page',
                                                'icon' => 'fa-file-text',
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
                    'administration_highlitingstates' => array(
                        'id' =>'highlitingstates',
                        'name' => __('Highliting states'),
                        'url' => SAFE::setBaseUrl('jx/admin/administration/highlitingstates'),
                        'capability' => 'access-administration-highlitingstates',
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
                        'name' => __('Background and overlay layers'),
                        'url' => '/jx/admin/administration/backgroundlayers',
                        'capability' => 'access-administration-backgroundlayers',
                        'icon' => 'leaf',
                    ),
             ),
            ),
        ),
        'regioncodes' => array(
                'id' =>'regioncodes',
                'name' => __('Region codes'),
                'capability' => 'access-administration',
                'icon' => 'cog',
                'dropdown' => 'dropdown_administration',
                'menu' => array(
                    'type' => 'tabs',
                    'items' => array(
                        'administration_pathsegments_code' => array(
                            'id' =>'pathsegments_code',
                            'name' => __('Survey class segments'),
                            'capability' => 'access-administration-clasrilsegments',
                            'icon' => 'cog',
                            'menu' => array(
                                'type' => 'affix',
                                'items' => array(
                                    'administration_classrilsegments' => array(
                                        'id' =>'classrilsegments',
                                        'name' => __('Survey class segments'),
                                        'url' => '/jx/admin/administration/clasrilsegments',
                                        'capability' => 'access-administration-clasrilsegments',
                                        'icon' => 'cog',
                                    ),
                                    'administration_tptratsegments' => array(
                                        'id' =>'tptratsegments',
                                        'name' => __('Typology path segments'),
                                        'url' => '/jx/admin/administration/tptratsegments',
                                        'capability' => 'access-administration-tptratsegments',
                                        'icon' => 'cog',
                                    ),
                                    'administration_tpfondosegments' => array(
                                        'id' =>'tpfondosegments',
                                        'name' => __('Bottom typology path segments'),
                                        'url' => '/jx/admin/administration/tpfondosegments',
                                        'capability' => 'access-administration-tpfondosegments',
                                        'icon' => 'cog',
                                    ),
                                    'administration_diffsegments' => array(
                                        'id' =>'diffsegments',
                                        'name' => __('Difficulty typology path segments'),
                                        'url' => '/jx/admin/administration/diffsegments',
                                        'capability' => 'access-administration-diffsegments',
                                        'icon' => 'cog',
                                    ),
                                    'administration_percorrsegments' => array(
                                        'id' =>'percorrsegments',
                                        'name' => __('Walkable path segments'),
                                        'url' => '/jx/admin/administration/percorrsegments',
                                        'capability' => 'access-administration-percorrsegments',
                                        'icon' => 'cog',
                                    ),
                                    'administration_ridpercsegments' => array(
                                        'id' =>'ridpercsegments',
                                        'name' => __('Reduction walkable path segments'),
                                        'url' => '/jx/admin/administration/ridpercsegments',
                                        'capability' => 'access-administration-ridpercsegments',
                                        'icon' => 'cog',
                                    ),
                                    'administration_morfsegments' => array(
                                        'id' =>'morfsegments',
                                        'name' => __('Morfology path segments'),
                                        'url' => '/jx/admin/administration/morfsegments',
                                        'capability' => 'access-administration-morfsegments',
                                        'icon' => 'cog',
                                    ),
                                    'administration_morfsegments' => array(
                                        'id' =>'morfsegments',
                                        'name' => __('Morfology path segments'),
                                        'url' => '/jx/admin/administration/morfsegments',
                                        'capability' => 'access-administration-morfsegments',
                                        'icon' => 'cog',
                                    ),
                                    'administration_ambientesegments' => array(
                                        'id' =>'ambientesegments',
                                        'name' => __('Ambient path segments'),
                                        'url' => '/jx/admin/administration/ambientesegments',
                                        'capability' => 'access-administration-ambientesegments',
                                        'icon' => 'cog',
                                    ),
                                    'administration_coptelsegments' => array(
                                        'id' =>'coptelsegments',
                                        'name' => __('GSM coverage segments'),
                                        'url' => '/jx/admin/administration/coptelsegments',
                                        'capability' => 'access-administration-coptelsegments',
                                        'icon' => 'cog',
                                    ),
                                    'administration_utenzasegments' => array(
                                        'id' =>'utenzasegments',
                                        'name' => __('Consumption path segments'),
                                        'url' => '/jx/admin/administration/utenzasegments',
                                        'capability' => 'access-administration-utenzasegments',
                                        'icon' => 'cog',
                                    ),
                                ),
                            ),
                        ),
                        'administration_considerablepoints_code' => array(
                            'id' =>'considerablepoints_codes',
                            'name' => __('Survey class points'),
                            'capability' => 'access-administration-clasrilsegments',
                            'icon' => 'map-marker',
                            'menu' => array(
                                'type' => 'affix',
                                'items' => array(
                                    'administration_ptinterpois' => array(
                                        'id' =>'ptinterpois',
                                        'name' => __('Point of interest considerable point'),
                                        'url' => '/jx/admin/administration/ptinterpois',
                                        'capability' => 'access-administration-ptinterpois',
                                        'icon' => 'cog',
                                    ),
                                    'administration_strutricpois' => array(
                                        'id' =>'strutricpois',
                                        'name' => __('Structure type considerable point'),
                                        'url' => '/jx/admin/administration/strutricpois',
                                        'capability' => 'access-administration-strutricpois',
                                        'icon' => 'cog',
                                    ),
                                    'administration_areeattrpois' => array(
                                        'id' =>'areeattrpois',
                                        'name' => __('Equipe area class'),
                                        'url' => '/jx/admin/administration/areeattrpois',
                                        'capability' => 'access-administration-areeattrpois',
                                        'icon' => 'cog',
                                    ),
                                    'administration_insediampois' => array(
                                        'id' =>'insediampois',
                                        'name' => __('Village class'),
                                        'url' => '/jx/admin/administration/insediampois',
                                        'capability' => 'access-administration-insediampois',
                                        'icon' => 'cog',
                                    ),
                                    'administration_ptacquapois' => array(
                                        'id' =>'ptacquapois',
                                        'name' => __('Water point class'),
                                        'url' => '/jx/admin/administration/ptacquapois',
                                        'capability' => 'access-administration-ptacquapois',
                                        'icon' => 'cog',
                                    ),
                                    'administration_tiposegnapois' => array(
                                        'id' =>'tiposegnapois',
                                        'name' => __('Signage type class'),
                                        'url' => '/jx/admin/administration/tiposegnapois',
                                        'capability' => 'access-administration-tiposegnapois',
                                        'icon' => 'cog',
                                    ),
                                    'administration_statosegnpois' => array(
                                        'id' =>'statosegnpois',
                                        'name' => __('Signage state class'),
                                        'url' => '/jx/admin/administration/statosegnpois',
                                        'capability' => 'access-administration-statosegnpois',
                                        'icon' => 'cog',
                                    ),
                                    'administration_fattdegrpois' => array(
                                        'id' =>'fattdegrpois',
                                        'name' => __('Degeneration cause class'),
                                        'url' => '/jx/admin/administration/fattdegrpois',
                                        'capability' => 'access-administration-fattdegrpois',
                                        'icon' => 'cog',
                                    ),
                                    'administration_ptsoccpois' => array(
                                        'id' =>'ptsoccpois',
                                        'name' => __('Rescue point class'),
                                        'url' => '/jx/admin/administration/ptsoccpois',
                                        'capability' => 'access-administration-ptsoccpois',
                                        'icon' => 'cog',
                                    ),
                                    'administration_coininfipois' => array(
                                        'id' =>'coininfipois',
                                        'name' => __('Start-end coincidence class'),
                                        'url' => '/jx/admin/administration/coininfipois',
                                        'capability' => 'access-administration-coininfipois',
                                        'icon' => 'cog',
                                    ),
                                    'administration_nuovsegnapois' => array(
                                        'id' =>'nuovsegnapois',
                                        'name' => __('New signage class'),
                                        'url' => '/jx/admin/administration/nuovsegnapois',
                                        'capability' => 'access-administration-nuovasegnapois',
                                        'icon' => 'cog',
                                    ),
                                    'administration_priointpois' => array(
                                        'id' =>'priointpois',
                                        'name' => __('Priority intervention class'),
                                        'url' => '/jx/admin/administration/priointpois',
                                        'capability' => 'access-administration-priointpois',
                                        'icon' => 'cog',
                                    ),
                                ),
                            ),
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
