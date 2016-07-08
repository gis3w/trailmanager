<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Myrouting extends Controller_Ajax_Base_Crud{
    
    protected $_pagination = FALSE;
    
    protected $_table = 'Myrouting';


    protected function _default_filter($orm)
    {
        $orm->where('user_id','=', $this->user->id);
    }

    protected function _edit()
    {
        // set user_id to current user
        $_POST['user_id'] = $this->user->id;
        $this->_base_edit();
        $this->jres->data = [
            'id' => $this->_orm->id
        ];
    }

    public function action_update()
    {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }

    public function action_delete()
    {
        $this->_orm->delete();
    }

    


}