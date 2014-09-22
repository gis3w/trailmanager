<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Path extends Controller_Ajax_Admin_Sheet_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Path";
    
    protected $_url_multifield_postname = 'url_path';
    protected $_url_multifield_nameORM = 'Url_Path';
    protected $_url_multifield_foreignkey = 'path_id';
    
    
    protected function _validation_url_multifiled() {
        
          // apply filter for numeric data
        // because validation is indipendent from ORM
        foreach(array('length','altitude_gap') as $field)
                if(isset($_POST[$field]))
                    $_POST[$field] = Filter::comma2point ($_POST[$field]);
        
        parent::_validation_url_multifiled();
    }

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
        if(isset($_POST['path_modes']))
            $this->_orm->setManyToMany('modes',$_POST['path_modes']);
    }
}