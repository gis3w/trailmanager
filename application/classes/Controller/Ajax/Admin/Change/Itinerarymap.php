<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Change_itinerarymap extends Controller_Ajax_Auth_Strict{
    
    protected $_res;


    public function action_index() {
        
        // si prepara la risposta vuota
        $this->_res['disabled'] = FALSE;
        $this->_res['value'] = array(
            'items' => array(),
        );
        
        if(isset($_GET['poi_id']) AND $_GET['poi_id'] != 'null')
            $this->_get_poi();
        
         if(isset($_GET['path_id']) AND $_GET['path_id'] != 'null')
            $this->_get_path();
         
         if(isset($_GET['area_id']) AND $_GET['area_id'] != 'null')
            $this->_get_area();
             
    }
    
    protected function _get_poi()
    {
        $poi_ids = preg_split('/,/',$_GET['poi_id']);
        $pois = ORMGIS::factory('Poi')
                ->where('id','IN',DB::expr('('. implode(',', $poi_ids).')'))
                ->find_all();
        $toData = array();
     
        
        foreach($pois as $poi)
        {
            $this->_res['value']['items'][] =array(
                    'id' => $poi->id,
                    'typology_id' => (int)$poi->typology_id,
                    'geoJSON' => json_decode($poi->asgeojson)
                );
        }
        
        
    }
    
     protected function _get_path()
    {
        $path_ids = preg_split('/,/',$_GET['path_id']);
        $paths = ORMGIS::factory('Path')
                ->where('id','IN',DB::expr('('. implode(',', $path_ids).')'))
                ->find_all();
        
        
        foreach($paths as $path)
        {
            $this->_res['value']['items'][] =array(
                    'id' => $path->id,
                    'color' => $path->color,
                    'geoJSON' => json_decode($path->asgeojson)
                );
        }

    }
    
    protected function _get_area()
    {
        $path_ids = preg_split('/,/',$_GET['area_id']);
        $paths = ORMGIS::factory('Area')
                ->where('id','IN',DB::expr('('. implode(',', $path_ids).')'))
                ->find_all();
        
        
        foreach($paths as $path)
        {
            $this->_res['value']['items'][] =array(
                    'id' => $path->id,
                    'typology_id' => (int)$path->typology_id,
                    'color' => $path->color,
                    'geoJSON' => json_decode($path->asgeojson)
                );
        }

    }
    
    public function after() {
        
        $this->jres->data->the_geom = $this->_res;
        
        parent::after();
    }
  
    
}