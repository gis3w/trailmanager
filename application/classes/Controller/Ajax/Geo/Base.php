<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Geo_Base extends Controller_Ajax_Data_Base{
    
   


    protected function _get_geo_base_data_from_orm($orm) {
        $toRes = array();
        
        $toRes['id'] = (int)$orm->id;
        $toRes['title'] = $orm->title;
        $toRes['typology_id'] = (int)$orm->typology_id;
        if($this->request->controller() == 'Path')
            $toRes['color'] = $orm->color;
        $toRes['geoJSON'] = json_decode($orm->asgeojson);
        
        return $toRes;
    }
    
}