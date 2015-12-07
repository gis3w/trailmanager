<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Geo_Base extends Controller_Ajax_Data_Base{
    
   


    protected function _get_geo_base_data_from_orm($orm) {
        $toRes = array();
        
        $toRes['id'] = (int)$orm->id;
        if(isset($orm->typology_id))
            $toRes['typology_id'] = $this->_get_main_typology($orm);
        if($this->request->controller() == 'Path' OR $this->request->controller() == 'Area')
        {
            $toRes['color'] = $orm->color;
            $toRes['width'] = $orm->width;
        }
        
        //adding centroids
        $toRes['centroids'] = array();
        /*
        $nGeometries = $orm->geo->numGeometries();
        $geoJsonAdapter = new GeoJSON();
        for($i = 1; $i <= $nGeometries; $i++)
            $toRes['centroids'][] = json_decode ($geoJsonAdapter->write(GEO::PostgisGentroid($orm->geo->geometryN($i))));
        */
        $toRes['geoJSON'] = json_decode($orm->asgeojson);
        
        if(isset($orm->max_scale))
            $toRes['max_scale'] = $orm->max_scale;
        // si aggiunge l'ensensione
        $toRes['extent'] = $orm->bbox;
        
        return $toRes;
    }

    protected function _get_main_typology($orm)
    {
        return (int)$orm->typology_id;
    }
    
}