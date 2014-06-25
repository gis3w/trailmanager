<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Geo_Base extends Controller_Ajax_Base_Crud_NoStrict_GET{
    
    public function before() {
        parent::before();
        
        // si impostano dei filtri
        if(!isset($_GET['filter']))
        {
            $_GET['filter'] = 'publish:true';
        }
        else
        {
            $_GET['filter'] .=",publish:true";
        }
            
    }


    protected function _get_geo_base_data_from_orm($orm) {
        $toRes = array();
        
        $toRes['id'] = (int)$orm->id;
        $toRes['title'] = $orm->title;
        $toRes['typology_id'] = (int)$orm->typology_id;
        $toRes['geoJSON'] = json_decode($orm->asgeojson);
        
        return $toRes;
    }
    
}