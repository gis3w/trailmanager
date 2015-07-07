<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe generica per utility e accessori DB
 *
 * @package    Gis3W
 * @category   Core
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2013 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

class Kohana_SAFEDB
{

    public static $CACHE_TB = array();

    public static function joinTbAssoc($mainTb,$joinInterTb)
    {
        return "(select ".$mainTb."_id from 
            (select ".$mainTb."_id, time_dissociazione ,row_number() over (partition by ".$mainTb."_id order by time_dissociazione desc) as rn from ".$joinInterTb.") as tb 
                where rn= 1 and time_dissociazione is null)";
    }

    public static function tbCache($tbname,$pkvalue,$field,$pkfield = 'id')
    {
        if(!isset(self::$CACHE_TB[$tbname]))
        {
            $td = ORM::factory($tbname)->find_all()->as_array($pkfield);
            self::$CACHE_TB[$tbname] = $td;
        }

        return isset(self::$CACHE_TB[$tbname][$pkvalue]) ? self::$CACHE_TB[$tbname][$pkvalue]->{$field} : NULL;
    }
}