<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Favoritepath extends Controller_Ajax_Base_Crud{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Path";


    public function action_create(){}

    public function action_update()
    {
        $this->_orm->add('users',$this->user);
    }

    public function action_delete()
    {
        $this->_orm->remove('users',$this->user);
    }
    


}