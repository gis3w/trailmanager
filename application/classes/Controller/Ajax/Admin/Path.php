<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Path extends Controller_Ajax_Admin_Sheet_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Path";
    
  
    protected function _data_edit() {
        parent::_data_edit();
        
        $this->_set_modes_edit();
        
    }
    
    protected function _single_request_row($orm) {
        $toRes = parent::_single_request_row($orm);
        
        $toRes['path_modes'] = array_keys($orm->modes->find_all()->as_array('id'));
        return $toRes;
    }


    protected function _set_modes_edit()
    {
        $this->_orm->setManyToMany('modes',$_POST['path_modes']);
    }
}