<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Administration_Highlitingstates extends Controller_Ajax_Admin_Administration_Base{
    
    protected $_pagination = FALSE;
    protected $_datastruct = "Administration_Highlitingstates";
    
    protected function _base_edit() {
         try
        {
            Database::instance()->begin();
            
            $this->_get_extra_validation();
            
            $this->_orm->values($_POST)->save($this->_extra_validation);
            
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
        $typologies = ORM::factory('Typology')->find_all();
        foreach ($typologies as $typology)
            SVG2PNGPinmap::instance($this->_orm->id, $typology->id);
        
    }
            
   
}