<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Administration_Aclroles extends Controller_Ajax_Admin_Administration_Roles{

    
    protected function _single_request_row($orm) {
        
        $capabilities_all = ORM::factory('Capability')->order_by('name')->find_all();
        $capabilities_role_id = array_keys($this->_orm->capabilities->find_all()->as_array('id'));


        $toRes = array();
        foreach($capabilities_all as $capability)
        {
            //var_dump($capability->id);
            $app = array($capability->name => FALSE);
            if(in_array($capability->id,$capabilities_role_id))
                    $app[$capability->name] = TRUE;
            $toRes[] = $app;
        }
        return $toRes;
    }
    
    protected function _get_list() {
        
    }
    
    protected function _edit() {
        
        // si recuperano gli id dei ruooli

        $capabilities = ORM::factory('Capability')->where('name','IN',array_keys($_POST))->find_all()->as_array('id');
        
         try
        {
            Database::instance()->begin();
            
            $this->_orm->setManyToMany('capabilities',$capabilities);
            
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
 
}