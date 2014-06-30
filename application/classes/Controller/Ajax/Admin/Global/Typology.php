<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Global_Typology extends Controller_Ajax_Base_Crud{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Global_Typology";
    
    
     protected function _base_edit()
    {
         try
        {
            Database::instance()->begin();
            
            $this->_get_extra_validation();
            
            //si recuparano ifile immagini da caricare
            $this->_get_subform_data('icon');
            $this->_get_subform_data('marker');
            
            $this->_orm->values($_POST)->save($this->_extra_validation);
            
            if(method_exists($this, '_save_environments'))
                    $this->_save_environments();
            
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
    
    protected function _get_subform_data($field) {
        $data = parent::_get_subform_data($field);
        $iconSet = FALSE;
        foreach($data as $row)
        {
            if(!isset($row['stato']))
                continue;
            
            if($row['stato'] == 'D')
            {
                @unlink(APPPATH."../".$this->_upload_path['typology_'.$field].$fileToDelete);
                $_POST[$field] = NULL;
            }
                
            
            if($row['stato'] == 'I')
            {
                $iconSet = TRUE;
                $_POST[$field] = $row[$field];
            }
        }
        
       

        
    }
    
    protected function _single_request_row($orm) {
        $res = parent::_single_request_row($orm);
        
        if($res['icon'] == '')
            $res['icon'] = NULL;
        
        if($res['marker'] == '')
            $res['marker'] = NULL;
        
        return $res;
    }
    
}