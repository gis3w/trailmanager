<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Change_itinerarymap extends Controller_Ajax_Auth_Strict{
    
    public function action_index() {
        if(isset($_GET['poi_id']) AND $_GET['poi_id'] != 'null')
            $this->_get_poi();
        
         if(isset($_GET['path_id']) AND $_GET['path_id'] != 'null')
            $this->_get_path();
             
    }
    
    protected function _get_poi()
    {
        $poi_ids = preg_split('/,/',$_GET['poi_id']);
        $pois = ORMGIS::factory('Poi')
                ->where('id','IN',DB::expr('('. implode(',', $poi_ids).')'))
                ->find_all();
        $toData = array();
        
        $toData['disabled'] = FALSE;
        $toData['value'] = array(
            'items' => array(),
        );
        
        foreach($pois as $poi)
        {
            $toData['value']['items'][] =array(
                    'id' => $poi->id,
                    'typology_id' => (int)$poi->typology_id,
                    'geoJSON' => json_decode($poi->asgeojson)
                );
        }
        
        $this->jres->data->the_geom = $toData;
    }
    
     protected function _get_path()
    {
        $path_ids = preg_split('/,/',$_GET['path_id']);
        $paths = ORMGIS::factory('Path')
                ->where('id','IN',DB::expr('('. implode(',', $path_ids).')'))
                ->find_all();
        
        $toData = array();
        
        $toData['disabled'] = FALSE;
        $toData['value'] = array(
            'items' => array(),
        );
        
        foreach($paths as $path)
        {
            $toData['value']['items'][] =array(
                    'id' => $path->id,
                    'typology_id' => (int)$path->typology_id,
                    'color' => $path->color,
                    'geoJSON' => json_decode($path->asgeojson)
                );
        }
        
        $this->jres->data->the_geom= $toData;
    }
  
    
}