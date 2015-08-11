<?php defined('SYSPATH') or die('No direct script access.');

//Route::set('print', 'print/(<controller>(/<action>(/<id>)))')
//	->defaults(array(
//                    'directory' => 'print',
//                    'controller' => 'home',
//                    'action'     => 'index',
//	));

Route::set('print', 'print/<directory>/<controller>(/<type>(/<id>))')
    ->filter(function($route, $params, $request)
    {
        $params['directory'] = 'Print/'.$params['directory'];
        if(isset($params['type']))
            $params['controller'] .= "_".ucfirst($params['type']);
        return $params;
    })
    ->defaults(array(
        'controller' => 'home',
        'action'     => 'index',
    ));

Route::set('print', 'print/<directory>/<controller>(/<id>)')
    ->filter(function($route, $params, $request)
    {
        $params['directory'] = 'Print/'.$params['directory'];
        return $params;
    })
    ->defaults(array(
        'controller' => 'home',
        'action'     => 'index',
    ));
