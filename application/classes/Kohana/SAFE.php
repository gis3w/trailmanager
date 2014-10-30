<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe Base che estente il core GT: Gestofauna
 *
 * @package    Gis3W
 * @category   Core
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2012 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

class Kohana_SAFE extends Kohana_Core
{
    
    const VERSION = '0.2.4';
    
    /**
     * Overload del metodo per avere anche la traduzione compresa
     * @param type $file
     * @param type $path
     * @param type $default
     * @return type 
     */
    public static function message($file, $path = NULL, $default = 'ERROR',$value = NULL)
    {
        
       $message = parent::message($file, $path, $default);
       
       $message = is_string($message) ? __($message): $message;
              
       return str_replace(':value', $value, $message);
           
    }
    
    public static function dbconn_params()
    {
        $res = array();
        $connection = Kohana::$config->load('database.default');
        $connection = $connection['connection'];
        
        // estrazione del dns
        list($type,$params) = preg_split("/:/", $connection['dsn']);
        $params = preg_split("/;/", $params);
        foreach($params as $p)
        {
            if($p)
            {
                list($k,$v) = preg_split("/=/",$p);
                $res[$k] = $v;
            }
            
        }
        
        $res['username'] = $connection['username'];
        $res['password'] = $connection['password'];
            
        return $res;
    }
    
    /**
     * Metodo per la costrusione delle date a seconda della localizzazione
     * @param type $d
     * @param type $m
     * @param type $Y
     * @return type
     */
    public static function date_mode($d = NULL,$m = NULL,$Y = NULL)
    {
        $date = isset($Y) AND isset($d) AND isset($m);
        
        switch(strtolower(I18n::lang()))
        {
            case "it":
            case"it-it":
               return $date ? $d."/".$m."/".$Y : 'd/m/Y'; 
            break;
        
            case "en":
            case"en-gb":
            case"en-us": 
              return $date ? $m."-".$d."-".$Y : 'm-d-Y';
            break;
        
            case "de":
            case"de-de":
                return $date ? $d."/".$m."/".$Y : 'd/m/Y';  
            break;
        
        }
    }
    
    public static function datehour_mode($d = NULL,$m = NULL,$Y = NULL)
    {
        $date = self::date_mode($d, $m, $Y);
        
        return $date." H:i";
        
    }
    
    public static function date_check_regex_mode()
    {

        switch(strtolower(I18n::lang()))
        {
           
            case "it":
            case"it-it":
            case "en":
            case"en-gb":
            case"en-us": 
            case "de":
            case"de-de":
              return "/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/";
            break;
        
        }
    }
    
    public static function date_hour_check_regex_mode()
    {
              return "/^[0-9]{2}\:[0-9]{2}$/";
          
    }
    
     public static function date2unixts($datestr)
    {
        $date =  DateTime::createFromFormat(self::date_mode(), $datestr);
        
        if($datestr != '' AND $date)
            return $date->getTimestamp();
        
        return NULL;
    }
    
    public static function datehour2unixts($datestr)
    {
        $date =  DateTime::createFromFormat(self::datehour_mode(), $datestr);
        
        if($datestr != '' AND $date)
            return $date->getTimestamp();
        
        return NULL;
    }
    
    /**
     *  Aggiunge modifica la date_ins e la data_mod nell'array passato
     * @param array $data
     */
    public static function setDatainsDatamod(&$data)
    {
        $time = time();
        unset($data['data_ins']);
        
        if(isset($data['id']))
        {
            $data['data_mod'] = $time;
        }
        else
        {
            $data['data_mod'] = $data['data_ins'] = $time;
        }
    }
    
    public static function getObjfromClass($instance)
    {
        $class = get_class($instance);
        $obj = preg_split("/_/", $class);
        $obj = $obj[count($obj) -1];
        
        return $obj;
    }
    public static function getDownloadClassByObj($instance)
    {
        return "Controller_Admin_Download_".self::getObjfromClass($instance);
    }
}