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
        'company' => array(
            'id' =>'company',
            'name' => __('Companies'),
            'url' => '/jx/company',
            'url_mobile' => 'company',
            'capability' => 'access-company',
            'icon' => 'suitcase',
        ),
//        'productionunit' => array(
//            'id' =>'productionunit',
//            'name' => __('Production units'),
//            'url' => '/jx/productionunit',
//            'url_mobile' => 'productionunit',
//            'capability' => 'access-productionunit',
//            'icon' => 'building',
//        ),
        'user' => array(
            'id' =>'user',
            'name' => __('Users'),
            'url' => '/jx/user',
            'url_mobile' => 'user',
            'capability' => 'access-user',
            'icon' => 'user',
        ),
         'global' => array(
            'id' =>'global',
            'name' => __('Global'),
            'capability' => 'access-global',
            'icon' => 'cog',
             'menu' => array(
                 'type' => 'tabs',
                 'items' => array(
                     /**----------------------------------------------------------------------------------------
                      *  Tab global -> company
                      ----------------------------------------------------------------------------------------**/
                     'company' => array(
                         'name' => __('Company'),
                         'icon' =>'cog',
                         'menu' => array(
                             'type' => 'affix',
                             'items' => array(
                                'global_atecocode' => array(
                                      'id' =>'atecocode',
                                      'name' => __('Ateco code'),
                                      'url' => '/jx/global/atecocode',
                                      'capability' => 'access-global-atecocode',
                                      'icon' => 'user',
                               ),
                               'global_interprofessionalfound' => array(
                                      'id' =>'interprofessionalfound',
                                      'name' => __('Interprofessional found'),
                                      'url' => '/jx/global/interprofessionalfound',
                                      'capability' => 'access-global-inteprofessionalfound',
                                      'icon' => 'user',
                               ),
                               'global_companyexpirationtype' => array(
                                      'id' =>'companyexpirationtype',
                                      'name' => __('Company expiration type'),
                                      'url' => '/jx/global/companyexpirationtype',
                                      'capability' => 'access-global-companyexpirationtype',
                                      'icon' => 'user',
                               ),
                           ), 
                         ),
                     ),
                     /**----------------------------------------------------------------------------------------
                      *  Tab global -> user
                      ----------------------------------------------------------------------------------------**/
                     'user' => array(
                         'name' => __('User'),
                         'icon' =>'cog',
                         'menu' => array(
                             'type' =>'affix',
                             'items' => array(
                                 'global_dpi' => array(
                                      'id' =>'dpi',
                                      'name' => __('Individual protection devices'),
                                      'url' => '/jx/global/dpi',
                                      'capability' => 'access-global-dpi',
                                      'icon' => 'user',
                               ),
                               'global_task' => array(
                                      'id' =>'task',
                                      'name' => __('Tasks'),
                                      'url' => '/jx/global/task',
                                      'capability' => 'access-global-task',
                                      'icon' => 'user',
                               ),
                               'global_qualification' => array(
                                      'id' =>'qualification',
                                      'name' => __('Qualifications'),
                                      'url' => '/jx/global/qualification',
                                      'capability' => 'access-global-qualification',
                                      'icon' => 'user',
                               ),
                               'global_userdocumenttype' => array(
                                      'id' =>'userdocumenttype',
                                      'name' => __('User document type'),
                                      'url' => '/jx/global/userdocumenttype',
                                      'capability' => 'access-global-userdocumenttype',
                                      'icon' => 'user',
                               ),
                             ),
                         ),
                     ),
                     /**----------------------------------------------------------------------------------------
                      *  Tab global -> productionunit
                      ----------------------------------------------------------------------------------------**/
                     'productionunit' => array(
                         'name' => __('Production unit'),
                         'icon' =>'cog',
                         'menu' => array(
                             'type' =>'affix',
                             'items' => array(
                                 'global_productionunittype' => array(
                                      'id' =>'productionunittype',
                                      'name' => __('Production unit type'),
                                      'url' => '/jx/global/productionunittype',
                                      'capability' => 'access-global-productionunittype',
                                      'icon' => 'user',
                               ),
                                'global_productionunitexpirationtype' => array(
                                        'id' =>'productionunitexpirationtype',
                                        'name' => __('Production unit expiration type'),
                                        'url' => '/jx/global/productionunitexpirationtype',
                                        'capability' => 'access-global-productionunitexpirationtype',
                                        'icon' => 'user',
                                 ),
                             ),
                         ),
                     ),
                     /**----------------------------------------------------------------------------------------
                      *  Tab global -> vehicle
                      ----------------------------------------------------------------------------------------**/
                     'vehicle' => array(
                         'name' => __('Vehicle'),
                         'icon' =>'cog',
                         'menu' => array(
                             'type' =>'affix',
                             'items' => array(
                                  'global_vehicletype' => array(
                                      'id' =>'vehicletype',
                                      'name' => __('Vehicle type'),
                                      'url' => '/jx/global/vehicletype',
                                      'capability' => 'access-global-vehicletype',
                                      'icon' => 'user',
                                    ),
                                     'global_vehicledocumenttype' => array(
                                           'id' =>'vehicledocumenttype',
                                           'name' => __('Vehicle document type'),
                                           'url' => '/jx/global/vehicledocumenttype',
                                           'capability' => 'access-global-vehicledocumenttype',
                                           'icon' => 'user',
                                    ),
                                    'global_vehicleexpirationtype' => array(
                                           'id' =>'vehicleexpirationtype',
                                           'name' => __('Vehicle expiration type'),
                                           'url' => '/jx/global/vehicleexpirationtype',
                                           'capability' => 'access-global-vehicleexpirationtype',
                                           'icon' => 'user',
                                    ),
                                    'global_vehicleusetype' => array(
                                           'id' =>'vehicleusetype',
                                           'name' => __('Vehicle use type'),
                                           'url' => '/jx/global/vehicleusetype',
                                           'capability' => 'access-global-vehicleusetype',
                                           'icon' => 'user',
                                    ),
                             ),
                         ),
                     ),
                     /**----------------------------------------------------------------------------------------
                      *  Tab global -> machine
                      ----------------------------------------------------------------------------------------**/
                      'machine' => array(
                         'name' => __('Machine'),
                         'icon' =>'cog',
                         'menu' => array(
                             'type' =>'affix',
                             'items' => array(
                                  'global_machinetype' => array(
                                      'id' =>'machinetype',
                                      'name' => __('Machine type'),
                                      'url' => '/jx/global/machinetype',
                                      'capability' => 'access-global-machinetype',
                                      'icon' => 'user',
                                ),
                                'global_machinedocumenttype' => array(
                                       'id' =>'machinedocumenttype',
                                       'name' => __('Machine document type'),
                                       'url' => '/jx/global/machinedocumenttype',
                                       'capability' => 'access-global-machinedocumenttype',
                                       'icon' => 'user',
                                ),
                                'global_machineusetype' => array(
                                          'id' =>'machineusetype',
                                          'name' => __('Machine use type'),
                                          'url' => '/jx/global/machineusetype',
                                          'capability' => 'access-global-machineusetype',
                                          'icon' => 'user',
                                   ),
                             ),
                         ),
                     ),
                      /**----------------------------------------------------------------------------------------
                      *  Tab global -> liftingmachine
                      ----------------------------------------------------------------------------------------**/
                      'liftingmachine' => array(
                         'name' => __('Lifting machine'),
                         'icon' =>'cog',
                         'menu' => array(
                             'type' =>'affix',
                             'items' => array(
                                'global_liftingmachinetype' => array(
                                      'id' =>'liftingmachinetype',
                                      'name' => __('Lifting machine type'),
                                      'url' => '/jx/global/liftingmachinetype',
                                      'capability' => 'access-global-liftingmachinetype',
                                      'icon' => 'user',
                                ),
                                'global_liftingmachinedocumenttype' => array(
                                       'id' =>'liftingmachinedocumenttype',
                                       'name' => __('Lifting machine document type'),
                                       'url' => '/jx/global/liftingmachinedocumenttype',
                                       'capability' => 'access-global-machinedocumenttype',
                                       'icon' => 'user',
                                ),
                                'global_liftingmachineexpirationtype' => array(
                                       'id' =>'liftingmachineexpirationtype',
                                       'name' => __('Lifting machine expiration type'),
                                       'url' => '/jx/global/liftingmachineexpirationtype',
                                       'capability' => 'access-global-machineexpirationtype',
                                       'icon' => 'user',
                                ),
                                'global_liftingmachineusetype' => array(
                                         'id' =>'liftingmachineusetype',
                                         'name' => __('Liftingmachine use type'),
                                         'url' => '/jx/global/liftingmachineusetype',
                                         'capability' => 'access-global-liftingmachineusetype',
                                         'icon' => 'user',
                                  ),
                             ),
                         ),
                     ),
                     /**----------------------------------------------------------------------------------------
                      *  Tab global -> plant
                      ----------------------------------------------------------------------------------------**/
                      'plant' => array(
                         'name' => __('Plant'),
                         'icon' =>'cog',
                         'menu' => array(
                             'type' =>'affix',
                             'items' => array(
                               'global_planttype' => array(
                                      'id' =>'planttype',
                                      'name' => __('Plant type'),
                                      'url' => '/jx/global/planttype',
                                      'capability' => 'access-global-planttype',
                                      'icon' => 'user',
                               ),
                                'global_plantexpirationtype' => array(
                                      'id' =>'plantexpirationtype',
                                      'name' => __('Plant expiration type'),
                                      'url' => '/jx/global/plantexpirationtype',
                                      'capability' => 'access-global-plantexpirationtype',
                                      'icon' => 'user',
                               ),
                             ),
                         ),
                     ),
                     /**----------------------------------------------------------------------------------------
                      *  Tab global -> chemical
                      ----------------------------------------------------------------------------------------**/
                      'chemical' => array(
                         'name' => __('Chemical'),
                         'icon' =>'cog',
                         'menu' => array(
                             'type' =>'affix',
                             'items' => array(
                                 'global_sentenceh' => array(
                                      'id' =>'sentenceh',
                                      'name' => __('Sentence H'),
                                      'url' => '/jx/global/sentenceh',
                                      'capability' => 'access-global-sentenceh',
                                      'icon' => 'user',
                               ),
                                'global_sentencep' => array(
                                      'id' =>'sentencep',
                                      'name' => __('Sentence P'),
                                      'url' => '/jx/global/sentencep',
                                      'capability' => 'access-global-sentencep',
                                      'icon' => 'user',
                               ),
                             ),
                         ),
                     ),
                 ),
             ),
             
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
