<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Highliting extends Controller_Ajax_Main{

    #use Controller_Ajax_Base_Cache_GET;

    protected $_base_route = 'jx';


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

        $highliting_poi = json_decode(Request::factory(Route::url($this->_base_route, array('controller' => 'highlitingpoi')))
                            ->execute()
                            ->body());

        $items = array(
            'Poi' => $highliting_poi->data->items,
        );
        
        $this->jres->data->tot_items = $this->jres->data->items_per_page =  count($highliting_poi->data->items);
        $this->jres->data->items = $items;
       
    }


}