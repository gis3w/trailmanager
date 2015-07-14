<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Global_Highlitingtypology extends Controller_Ajax_Base_Crud{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Global_Highlitingtypology";
    
    protected $_upload_path = array(
         'highliting_typology_icon' => 'highlitingtypologyicon',
     );
    
    
     protected function _base_edit()
    {
         try
        {
            Database::instance()->begin();
            
            $this->_get_extra_validation();
            
            //si recuparano ifile immagini da caricare
            $this->_get_icon_marker();
            
            $this->_orm->values($_POST)->save($this->_extra_validation);

            // we update or save mappins relative
            $this->_update_mappins();
            
            Database::instance()->commit();
        }
         catch (Database_Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }   
        catch (ORM_Validation_Exception $e)
        {
            Database::instance()->rollback();
            
            $this->_validation_error($e);
        }
        catch (Validation_Exception $e)
        {
            Database::instance()->rollback();
            
            $this->_validation_error($this->vErrors);
            
        }
    }

    protected function _update_mappins()
    {
        // for evevy state we bould mappin!
        $highliting_states = ORM::factory('Highliting_State')->find_all();
        foreach ($highliting_states as $highliting_state)
            SVG2PNGPinmap::instance($highliting_state->id, $this->_orm->id);

    }
    
    protected function _get_icon_marker() {
        
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
                    @unlink(APPPATH."../".$this->_upload_path['highliting_typology_'.$field].$data->name);
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