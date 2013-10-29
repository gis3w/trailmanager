<?php defined('SYSPATH') or die('No direct script access.');

abstract class Database_Result extends Kohana_Database_Result {
    
    public function idArray($filter = NULL)
    {
        
        $idarr = array();
        
        foreach($this as $res)
        {
            $add = TRUE;
            if(isset($filter))
            {
                $add = call_user_func($filter,$res);   
            }
            
            if($add)
                $idarr[] = $res->id;
        }
        
        sort($idarr);
        
        return $idarr;
    }
    
   
    public function getQuery()
    {
        return $this->_query;
    }
    
}
