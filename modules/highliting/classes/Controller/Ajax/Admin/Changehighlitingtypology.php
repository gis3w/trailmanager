<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Changehighlitingtypology extends Controller_Ajax_Auth_Strict{

    protected $_pagination = FALSE;

    public function action_create() {
        
    }
    
    public function action_update() {
        
    }
    
    public function action_delete() {
        
    }
    
    protected function _get_item()
    {
        unset($this->jres->data->items);
        $this->jres->data = array(
            'pt_inter' => [
                'hidden' => FALSE,
            ]
        );
    }
    protected function _get_list()
    {


        unset($this->jres->data->items);
        $this->jres->data = array(
            'pt_inter' => [
                'hidden' => FALSE,
            ]
        );


    }
  
}