<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Base_Crud_Directory extends Controller_Ajax_Base_Crud{
    
    protected function _ACL() {
        
        $this->_directory_ACL();
        
        parent::_ACL();
    }
    

}