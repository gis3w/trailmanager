<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Highlitingpoi extends Controller_Ajax_Admin_Sheet_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Highlitingpoi";

    protected function _data_edit()
    {
        Filter::emptyPostDataToNULL();

        $this->_set_the_geom_edit();
        $this->_orm->values($_POST);
        $this->_orm->save();

        $this->_save_subforms_1XN();

    }
    
  
}