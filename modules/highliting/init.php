<?php defined('SYSPATH') or die('No direct script access.');

 // per errore in sviluppo/produzione ajax
Route::set('jx/pathsclose', 'jx/pathsclose/<lon>/<lat>', array('lon' => '[\d]+(|\.[\d]+)', 'lat' => '[\d]+(|\.[\d]+)'))
->defaults(array(
    'directory' => 'ajax',
    'controller' => 'pathsclose',
    'action'	 => 'index',
));
