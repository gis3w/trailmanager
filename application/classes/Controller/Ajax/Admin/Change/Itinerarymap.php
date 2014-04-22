<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Change_itinerarymap extends Controller_Ajax_Auth_Strict{
    
    public function action_index() {
        if(isset($_GET['poi_id']))
            $this->_get_poi();
        
         if(isset($_GET['path_id']))
            $this->_get_path();
             
    }
    
    protected function _get_poi()
    {
        $poi = ORMGIS::factory('Poi',$_GET['poi_id']);
        $toData = array();
        
        $toData['disabled'] = FALSE;
        $toData['value'] = array(
            'items' => array(
                array(
                    'id' => $poi->id,
                    'typology_id' => (int)$poi->typology_id,
                    'geoJSON' => json_decode($poi->asgeojson)
                ),
            ),
        );
        
        $this->jres->data = $toData;
    }
    
     protected function _get_path()
    {
        $path = ORMGIS::factory('Path',$_GET['path_id']);
        $toData = array();
        
        $toData['disabled'] = FALSE;
        $toData['value'] = array(
            'items' => array(
                array(
                    'id' => $pathi->id,
                    'typology_id' => (int)$path->typology_id,
                    'geoJSON' => json_decode($path->asgeojson),
                    'color' => $path->color
                ),
            ),
        );
        
        $this->jres->data = $toData;
    }
  
    
}