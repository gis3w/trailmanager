<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/Kohana/Core'.EXT;

if (is_file(APPPATH.'classes/Kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/Kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/Kohana'.EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('Europe/Rome');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'it_IT.utf-8');
setlocale(LC_NUMERIC, 'en_US.utf8');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Optionally, you can enable a compatibility auto-loader for use with
 * older modules that have not been updated for PSR-0.
 *
 * It is recommended to not enable this unless absolutely necessary.
 */
//spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
 I18n::lang('it');
 




Cookie::$salt = 'cosoweb7890';

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */
Kohana::init(array(
	'base_url'   => '/',
                  'index_file' => FALSE,
                  'caching' => TRUE,
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

// uploader:
require_once Kohana::find_file('vendors', 'jQuery-File-Upload-8.2.1/UploadHandler');
require_once APPPATH.'../vendor/autoload.php';

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
    'auth'       => MODPATH.'auth',       // Basic authentication
	//'cache'      => MODPATH.'cache',      // Caching with multiple backends
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	 'database'   => MODPATH.'database',   // Database access
	'image'      => MODPATH.'image',      // Image manipulation
	'minion'     => MODPATH.'minion',     // CLI Tasks
    'orm'        => MODPATH.'orm',        // Object Relationship Mapping
    'ormgis'        => MODPATH.'ormgis',        // Object Relationship Mapping for Postgis table
    'geo'        => MODPATH.'geo',        // Geo funaction helper
	// 'unittest'   => MODPATH.'unittest',   // Unit testing
	'userguide'  => MODPATH.'userguide',  // User guide and API documentation
    'formo'  => MODPATH.'formo',
    'gis3w' => MODPATH.'gis3w',
    'restapi'  => MODPATH.'restapi',  // REST api systems
    'tree'  => MODPATH.'tree',  // tree data sctructure
    'pagination' => MODPATH.'pagination',
    'highliting' => MODPATH.'highliting',
    'print' => MODPATH.'print',
    'geoexport' => MODPATH.'geoexport',
    'minion' => MODPATH.'minion',
	'routing' => MODPATH.'routing'
	));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */


Route::set('jx/upload', 'jx/upload(/<controller>(/<id>))')
	->defaults(array(
                    'directory' => 'Ajax/Upload',
                    'controller' => 'home',
                    'action'     => 'index',
	));


Route::set('jx/document', 'jx/document(/<controller>(/<id>(/<filtro>)))')
	->defaults(array(
                    'directory' => 'Ajax/Document',
                    'controller' => 'home',
                    'action'     => 'index',
	));

Route::set('jx/admin/administration', 'jx/admin/administration(/<controller>(/<id>(/<filtro>)))')
	->defaults(array(
                    'directory' => 'Ajax/Admin/Administration',
                    'controller' => 'home',
                    'action'     => 'index',
	));

Route::set('jx/admin/global', 'jx/admin/global(/<controller>(/<id>(/<filtro>)))')
	->defaults(array(
                    'directory' => 'Ajax/Admin/Global',
                    'controller' => 'home',
                    'action'     => 'index',
	));


Route::set('jx/admin/upload', 'jx/admin/upload(/<controller>(/<id>(/<filtro>)))')
	->defaults(array(
                    'directory' => 'Ajax/Admin/Upload',
                    'controller' => 'home',
                    'action'     => 'index',
	));

Route::set('jx/admin/change', 'jx/admin/change(/<controller>(/<id>(/<filtro>)))')
	->defaults(array(
                    'directory' => 'Ajax/Admin/Change',
                    'controller' => 'home',
                    'action'     => 'index',
	));

Route::set('jx/admin', 'jx/admin(/<controller>(/<id>(/<filtro>)))')
	->defaults(array(
                    'directory' => 'Ajax/Admin',
                    'controller' => 'home',
                    'action'     => 'index',
	));

Route::set('jx/data', 'jx/data/(<controller>(/<id>(/<filtro>)))')
	->defaults(array(
                    'directory' => 'Ajax/Data',
                    'controller' => 'home',
                    'action'     => 'index',
	));

Route::set('jx/geo', 'jx/geo/(<controller>(/<id>(/<filtro>)))')
	->defaults(array(
                    'directory' => 'Ajax/Geo',
                    'controller' => 'home',
                    'action'     => 'index',
	));

Route::set('jx/media', 'jx/media/(<controller>(/<id>(/<filtro>)))')
	->defaults(array(
                    'directory' => 'Ajax/Media',
                    'controller' => 'home',
                    'action'     => 'index',
	));

Route::set('admin/download/qrcode', 'admin/download/qrcode(/<controller>(/<id>))')
	->defaults(array(   
                                'directory' => 'Admin/Download/Qrcode',
		'action'     => 'index',
	));


Route::set('admin/download', 'admin/download(/<controller>(/<action>(/<file>)))',array('file' => '.*'))
	->defaults(array(   
                                'directory' => 'Admin/Download',
		'controller' => 'home',
		'action'     => 'index',
	));

Route::set('admin', 'admin(/<controller>(/<action>(/<id>)))')
	->defaults(array(
                                'directory' => 'Admin',
		'controller' => 'home',
		'action'     => 'index',
	));

Route::set('download', 'download(/<controller>(/<action>(/<file>)))',array('file' => '.*'))
	->defaults(array(   
                                'directory' => 'Download',
		'controller' => 'home',
		'action'     => 'index',
	));

Route::set('jx', 'jx/(<controller>(/<id>(/<filtro>)))')
	->defaults(array(
                    'directory' => 'ajax',
                    'controller' => 'home',
                    'action'     => 'index',
	));

Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'home',
		'action'     => 'index',
	));


/**
 * AGGIUNTA DEI PARAMETRI GENERALI SALVATI DB
*/
$parms = ORM::factory('Global_Config')->find_all()->as_array('parametro');

foreach($parms as $parametro => $valore)
    if(is_object($valore))
    {
        define(strtoupper($parametro), $valore->valore); 
    }
    else
    {
        define(strtoupper($parametro), $valore); 
    }

    /**
     * Adding $_SERVER paramenters 
     */
    if(isset($_SERVER['HTTP_HOST']))
        define('HTTP_HOST',$_SERVER['HTTP_HOST']);


/**
 * ADD ROLES
 */
$parms = ORM::factory('Role')->find_all()->as_array('name');

foreach($parms as $parametro => $valore)
    define('ROLE_'.Inflector::underscore(strtoupper($parametro)), $valore->id);

