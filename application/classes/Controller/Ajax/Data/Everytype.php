<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Data_Everytype extends Controller_Ajax_Main{

    use Controller_Ajax_Base_Cache_GET;
    
    protected $_base_route = 'jx/data';


    public function action_delete() {
       throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));  
    }
    
    public function action_update() {
         throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }
    
    public function action_create() {
         throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }
    
    public function action_index() {
         if(is_numeric($this->id))
            throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
         
         $this->_get_all_highlightins();
    }
    
    protected function _get_all_highlightins()
    {
        $poi = json_decode(Request::factory(Route::url($this->_base_route, array('controller' => 'poi')))
                            ->execute()
                            ->body());
        $path = json_decode(Request::factory(Route::url($this->_base_route, array('controller' => 'path')))
                            ->execute()
                            ->body());
        $area = json_decode(Request::factory(Route::url($this->_base_route, array('controller' => 'area')))
                            ->execute()
                            ->body());
        $items = array(
            'Poi' => $poi->data->items,
            'Path' => $path->data->items,
            'Area' => $area->data->items
        );
        
        $this->jres->data->tot_items = $this->jres->data->items_per_page =  count($poi->data->items) + count($path->data->items) + count($area->data->items);
        $this->jres->data->items = $items;
       
    }


}