<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package    GIS3W/restapi
 * @category   Controller
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2012 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */
class RestAPI {
    
    public static function error(Exception $e)
    {
        Kohana_Exception::handler($e);
    }
    
    public static function __callStatic($name, $arguments) {       
        self::$name($arguments);
    }
        
    
}