<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Data_Base extends Controller_Ajax_Base_Crud_GET{
    
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


    protected function _get_base_data_from_orm($orm) {
        $toRes = $orm->as_array();
        
        unset(
                $toRes['the_geom'],
                $toRes['asbinary'],
                $toRes['astext'],
                $toRes['asgeojson'],
                $toRes['box2d'],
                $toRes['extent'],
                $toRes['x'],
                $toRes['y'],
                $toRes['centroid']

                );
        
        $toRes['id'] = (int)$orm->id;
        $toRes['typology_id'] = (int)$orm->typology_id;
        
        
        
        return $toRes;
    }
    
}