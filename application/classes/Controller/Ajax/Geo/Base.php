<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Geo_Base extends Controller_Ajax_Base_Crud_GET{
    
    public function before() {
        parent::before();
        
        $this->_filters['publish'] = 'true';
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