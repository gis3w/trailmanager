<?php defined('SYSPATH') or die('No direct script access.');

Route::set('export', 'export/<type>/<directory>(/<controller>(/<id>))')
    ->filter(function($route, $params, $request)
    {
        if(isset($params['controller']))
        {
            $params['directory'] = 'Export/'.$params['directory'];
            $params['controller'] .= "_".ucfirst($params['type']);
        }
        else
        {
            $params['directory'] = 'Export';
            $params['controller'] = $params['directory']."_".ucfirst($params['type']);
        }

        return $params;
    })
    ->defaults(array(
        'controller' => 'home',
        'action'     => 'index',
    ));

Route::set('export', 'export/<type>/<controller>(/<id>)')
    ->filter(function($route, $params, $request)
    {
        $params['directory'] = 'Export/'.$params['controller'];
        $params['controller'] = ucfirst($params['type']);
        return $params;
    })
    ->defaults(array(
        'controller' => 'home',
        'action'     => 'index',
    ));