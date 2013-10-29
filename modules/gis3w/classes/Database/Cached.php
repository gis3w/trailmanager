<?php defined('SYSPATH') or die('No direct script access.');

abstract class Database_Result_Cached extends Kohana_Database_Result_Cached{
    
    public function getQuery()
    {
        return $this->_query;
    }
}
