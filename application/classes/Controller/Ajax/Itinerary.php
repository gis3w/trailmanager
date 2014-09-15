<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Itinerary extends Controller_Ajax_Base_Crud_GET{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Itinerary";
    
    protected $_pois = array();
    protected $_paths = array();


    
     protected function _single_request_row($orm) {
        $toRes = parent::_single_request_row($orm);
        
        // si aggiungo i codici ateco
        foreach(array('pois','paths','areas') as $alias)
        {
            $datas = $orm->$alias->find_all();
            foreach($datas as $data)
           {
               $toRes[$alias][] = array(
                   "id" => $data->id,
               );
           }
        }
         
        
        return $toRes;
    }
    
}