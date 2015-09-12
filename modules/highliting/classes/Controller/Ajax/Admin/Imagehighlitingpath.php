<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Imagehighlitingpath extends Controller_Ajax_Base_Crud{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Image_Highliting_Path";
    
    protected $_orderings = array(
        'norder'
    );




    protected function _edit() {
        $datastruct = $this->_datastruct;
        Filter::emptyArrayPostDataToStringEmpty(array($datastruct::$preKeyField.'-file'));
       
        $this->_validation_orm();
    }
    
    protected function _single_request_row($orm) {
        $toRes = parent::_single_request_row($orm);
        
        $datastruct = $this->_datastruct;
        $preK = '';
        if(isset($datastruct) AND isset($datastruct::$preKeyField))   
            $preK = $datastruct::$preKeyField.'-';

        
        unset(
                $toRes[$preK.'data_ins'],
                $toRes[$preK.'data_mod']
                );
        
        return $toRes;
    }

        public function action_delete(){}
  
}