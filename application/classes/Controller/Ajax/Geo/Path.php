<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Geo_Path extends Controller_Ajax_Base_Crud_GET{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Path";
    
    
    protected function _single_request_row($orm) {
        $toRes = array();
        
        $toRes['id'] = (int)$orm->id;
        $toRes['title'] = $orm->title;
        $toRes['typology_id'] = (int)$orm->typology_id;
        $toRes['geoJSON'] = json_decode($orm->asgeojson);
        
        return $toRes;
    }
  
    
}