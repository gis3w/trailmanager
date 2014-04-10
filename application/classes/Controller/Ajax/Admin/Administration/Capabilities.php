<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Administration_Capabilities extends Controller_Ajax_Admin_Administration_Base{
    
    protected $_pagination = FALSE;


    protected $_table = 'Capability';
    protected $_table_name = 'capabilities';
    
    protected $_roles = array();

 
    public function _single_request_row($orm) {
       $arr = parent::_single_request_row($orm);
           
       $roles=  $orm->roles->find_all();
       foreach($roles as $role)
           $arr['roles'][] = $role->as_array();
       
       return $arr;
   }
   
     protected function _edit()
    {
        try
        {
            Database::instance()->begin();
            
            $this->_orm->values($_POST)->save();
            
            // si esegue il salvataggio dei ruoli
            if(isset($_POST['roles']) AND  is_array($_POST['roles']) ) 
                $this->_roles = $_POST['roles'];
            $this->_orm->setManyToMany('roles', $this->_roles);
           
            
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