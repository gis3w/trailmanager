<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe generica per filtraggio dati _POST _GET e altro formato
 *
 * @package    Gis3W
 * @category   Filter
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2013 Gis3W
 */

class Kohana_Filter
{


 /**
     * Metodo che filtra i parametri serializzati dentro i post
     * @param type $postData
     * @return array 
     */
    public static function serializePostData($postData)
    {
        
        $app = array();
        $count = 0;
        $m= "/(\[.*?\])/";
        $a = preg_replace_callback($m,function($match) use (&$count,&$app){
            $res =  '#ARRAY'.(string)$count;
            $app[$res] = $match[0];
            $count++;
            return $res;
        },$postData);
        
        $m= "/(\{.*?\})/";
        $r = preg_replace_callback($m,function($match) use (&$count,&$app){
            $res =  '#OBJEC'.(string)$count;
            $app[$res] = $match[0];
            $count++;
            return $res;
        },$a);

        
        $eArr = array();

        foreach(preg_split('/;/', $r) as $row)
        {
            $arr = array();
            if($row)
            {
                // si ridivide sulla base
                foreach(preg_split('/,/', $row) as $n => $data)
                {
                    if($data AND preg_match('/:/', $data))
                    {
                        list($field,$value) = preg_split('/:/',$data);
                        if(in_array(substr($value, 0,6),array('#ARRAY','#OBJEC')))
                        {
                            $arr[$field] = self::serializePostData(substr($app[$value],1,-1));
                        }  
                        else
                        {
                            $arr[$field] = $value;
                        }
                        
                    }
                    else
                    {
                        $arr[] = $data;
                    }

                }

                $eArr[] = $arr;
            }
        }
        
        if(count($eArr) === 1 AND key($eArr) === 0)
            return $eArr[0];
        
        return $eArr;
    }
    
    public static function get_PDO_paramenters($config_file = "default")
    {
        $dbCon = Kohana::$config->load('database.'.$config_file);
        $dsnString = preg_split("/:/", $dbCon['connection']['dsn']);
        $dsnString = $dsnString[1];
        $dsnParamString = preg_split("/;/", $dsnString);
        $dbConParameters = array();
        foreach($dsnParamString as $parameterString)
        {
            if($parameterString)
            {
                list($key,$value) = preg_split("/=/", $parameterString);
                $dbConParameters[$key] = $value;
            }
            
        }
        $dbConParameters['username'] = $dbCon['connection']['username'];
        $dbConParameters['password'] = $dbCon['connection']['password'] ;
        
        return $dbConParameters;
    }
    
    public static function hasToManyActions($new,$actual)
    {
        return array(
            'toRemove' => array_values(array_diff($actual, $new)),
            'toAdd' => array_values(array_diff($new, $actual)),
         );
    }
    
    /**
     * Unset dei valori vuoti dal client
     */
    public static function emptyPostDataToNULL($keys = NULL, $keys_NULL =  [])
    {
        // ne caso di chiavi da acìzzerare
        
        if(isset($keys))
        {
            $toReset = array_intersect_key($_POST, array_flip($keys));
        }
        else
        {
            $toReset = $_POST;
        }
        $tounset = array();
        foreach($toReset as $key =>$value)
            if($value === '')
                $tounset[] = $key;
            
        foreach($tounset as $key)
        {
            if(in_array($key,$keys_NULL))
            {
                $_POST[$key] = NULL;
            }
            else
            {
                unset($_POST[$key]);
            }
        }

    }
    
    /**
     * To empty string array POST dtat empty
     */
    public static function emptyArrayPostDataToStringEmpty($keys = NULL)
    {
        // ne caso di chiavi da acìzzerare
        
        if(isset($keys))
        {
            $toReset = array_intersect_key($_POST, array_flip($keys));
        }
        else
        {
            $toReset = $_POST;
        }
        
        $toempty = array();
        foreach($toReset as $key =>$value)
        {
            if($value === '[]')
                $toempty[] = $key;
        }
           
        foreach($toempty as $key)
            $_POST[$key] = '';
    }
    
    
    static function comma2point($number)
    {
        return preg_replace('/,/', ".", $number);
    }
    
    static function  point2comma($number)
    {
        return preg_replace('/\./', ",", $number);
    }
}

