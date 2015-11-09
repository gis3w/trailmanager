<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Data_Base extends Controller_Ajax_Base_Crud_NoStrict_GET{

    
    public function before() {
        parent::before();

        if($this->request->controller() != 'Itinerary')
        {
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
        
            
    }
    
    protected function _get_item() {
        // si controllo se il dato è pubblicato altrimenti si mette un 404
        if($this->request->controller() != 'Itinerary' AND !$this->_orm->publish)
            throw new HTTP_Exception_404();
        
            parent::_get_item();
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
                $toRes['centroid'],
                $toRes['publish'],
                $toRes['max_scale']
                );
        
        // si aggiunge anche la foto principale
        $main_image = $orm->images
                ->order_by('norder','ASC')
                ->find();
        
        $toRes['thumb_main_image'] = isset($main_image->id) ? $this->_thumb_uri.$main_image->file : NULL;
        
        $toRes['id'] = (int)$orm->id;
        if(isset($orm->typology_id))
            $toRes['typology_id'] = (int)$orm->typology_id;
        
        // adding urls

            
        
        switch($this->request->controller())
        {
            case "Itinerary":
               // si aggiungon gli id dei paths e dei pois
                $toRes['paths'] = array_keys($orm->paths->where('publish','IS',DB::expr('true'))->find_all()->as_array('id'));
                $toRes['pois'] = array_keys($orm->pois->where('publish','IS',DB::expr('true'))->find_all()->as_array('id'));
                $toRes['areas'] = array_keys($orm->areas->where('publish','IS',DB::expr('true'))->find_all()->as_array('id'));
             break;
         
            default:
                $toRes['itineraries'] = array_keys($orm->itineraries->find_all()->as_array('id'));
                // si aggiungono le subtipologie
                $toRes['typologies'] = array_keys($orm->typologies->find_all()->as_array('id'));
        }       
        
        
        
        return $toRes;
    }
    
}