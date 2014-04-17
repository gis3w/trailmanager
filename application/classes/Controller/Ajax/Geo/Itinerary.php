<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Geo_Itinerary extends Controller_Ajax_Geo_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Itinerary";
    
    
    protected function _single_request_row($orm) {
        $toRes = array();
        
        $toRes['id'] = (int)$orm->id;
        $toRes['name'] = (int)$orm->name;
        $toRes['description'] = $orm->description;

        
        
        // si inseriscono i poi e i path:
        foreach(array('pois','paths') as $geo)
        {
            $geoOrms = $orm->$geo->find_all();
            $toRes[$geo] = array();
            foreach($geoOrms as $geoOrm)
                $toRes[$geo][] = $this->_get_geo_base_data_from_orm($geoOrm);
            
        }
        
        return $toRes;
    }
  
    
}