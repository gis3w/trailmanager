<?php defined('SYSPATH') or die('No direct script access.');



class Kohana_Check 
{
    public static function afterThan($date1,$date2)
    {
        $date1 =  DateTime::createFromFormat(SAFE::date_mode(), $date1);
        $date2 =  DateTime::createFromFormat(SAFE::date_mode(), $date2);
        
        return $date1 >=$date2;
    }
    
     public static function beforeThan($date1,$date2)
    {
        $date1 =  DateTime::createFromFormat(SAFE::date_mode(), $date1);
        $date2 =  DateTime::createFromFormat(SAFE::date_mode(), $date2);
        
        return $date1 <=$date2;
    }
}