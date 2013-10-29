<?php defined('SYSPATH') or die('No direct script access.');


Route::set('restapi', 'api/<version>/(<controller>(/<id>(/<filtro>)))',array('version' => "[0-9].[0-9]"))
    ->filter(function($route, $params, $request)
    {
        // Prefix the method to the action name
        //$params['action'] = strtolower($request->method()).'_'.$params['action'];
        $params['directory'] = "Api/".str_replace(".", "", $params['version']);
        return $params; // Returning an array will replace the parameters
    })
    ->defaults(array(
        'controller' => 'home',
        'action'     => 'index',
    ));

    
 // per errore in sviluppo/produzione ajax
Route::set('restapi/error', 'api/error/<action>(/<message>(/<line>(/<file>)))', array('action' => '[0-9]++', 'line' => '[0-9]++','message'=>'[^/;?\n]++', 'file' => '.+'))
->defaults(array(
    'directory' => 'api',
    'controller' => 'error',
    'action'	 => 'index',
));

Route::set('rest/error', 'rest/error/<action>(/<message>(/<line>(/<file>)))', array('action' => '[0-9]++', 'line' => '[0-9]++','message'=>'[^/;?\n]++', 'file' => '.+'))
->defaults(array(
    'directory' => 'REST',
    'controller' => 'error',
    'action'	 => 'index',
));