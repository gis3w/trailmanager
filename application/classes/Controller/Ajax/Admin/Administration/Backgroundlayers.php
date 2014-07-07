<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Administration_Backgroundlayers extends Controller_Ajax_Admin_Administration_Base{
    
    protected $_pagination = FALSE;
    protected $_datastruct = "Administration_Backgroundlayers";
    
    
    protected function _base_edit()
    {
         try
        {
            Database::instance()->begin();
            
            $this->_get_extra_validation();
            
            $this->_orm->values($_POST)->save($this->_extra_validation);
            
            $this->_orm->setManyToMany('sections',$_POST['sections']);

            
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
    
    protected function _single_request_row($orm) {
        $res = parent::_single_request_row($orm);
        
        $res['sections'] = array_keys($orm->sections->find_all()->as_array('id'));
        
        return $res;

    }

   
}