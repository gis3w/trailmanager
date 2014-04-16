<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Geo_Itinerary extends Controller_Ajax_Base_Crud_GET{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Itinerary";
    
    
    protected function _single_request_row($orm) {
        $toRes = array();
        
        $toRes['id'] = (int)$orm->id;
        $toRes['name'] = (int)$orm->name;
        $toRes['description'] = $orm->description;

        // si inseriscono i poi:
        $pois = $orm->pois->find_all();
        $toRes['pois'] = array();
        foreach($pois as $poi)
        {
            $app = array();
            $app['id'] = (int)$poi->id;
            $app['title'] = (int)$poi->title;
            $app['typology_id'] = $poi->typology_id;
            $app['geoJSON'] = json_decode($poi->asgeojson);
            
            $toRes['pois'][] = $app;
        }
        
        // si inseriscono i poi:
        $paths = $orm->paths->find_all();
        $toRes['paths'] = array();
        foreach($paths as $path)
        {
            $app = array();
            $app['id'] = (int)$path->id;
            $app['title'] = (int)$path->title;
            $app['typology_id'] = $path->typology_id;
            $app['geoJSON'] = json_decode($path->asgeojson);
            
            $toRes['paths'][] = $app;
        }
        
        return $toRes;
    }
  
    
}