<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Administration_Roles extends Controller_Ajax_Admin_Administration_Base{
    
    protected $_pagination = FALSE;
    protected $_datastruct = "Administration_Roles";
   
     
    public function _get_data() {

         // per il _get_item()
       if(is_numeric($this->id))
           return parent::_get_data();
       
       return $this->_orm->where('name','!=','login');
    }
}