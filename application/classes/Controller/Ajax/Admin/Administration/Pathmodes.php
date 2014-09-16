<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Administration_Pathmodes extends Controller_Ajax_Admin_Administration_Base{
    
    protected $_pagination = FALSE;
    protected $_datastruct = "Administration_Pathmodes";
    
    protected function _edit() {
        $this->_get_icon();
        parent::_edit();
    }


    protected function _get_icon() {
        
        $fields = array('icon');
        
         foreach($fields as $field)
        {
            $postField = json_decode($_POST[$field]);
            if(empty($postField))
            {
                $_POST[$field] = '';
                continue;
            }
            
            foreach ($postField as $data)
            {
                 if($data->stato == 'D')
                {
                    @unlink(APPPATH."../".$this->_upload_path['typology_'.$field].$data->name);
                    $_POST[$field] = NULL;
                }


                if($data->stato == 'I' OR $data->stato == 'U')
                {
                    $_POST[$field] = $data->name;
                }
            }
           
        }
     }
     
      protected function _single_request_row($orm) {
        $res = parent::_single_request_row($orm);
        
        if($res['icon'] == '')
            $res['icon'] = NULL;
        
        return $res;
    }
}