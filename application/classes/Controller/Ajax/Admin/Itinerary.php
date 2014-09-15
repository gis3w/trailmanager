<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Itinerary extends Controller_Ajax_Base_Crud{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Itinerary";
    
    protected $_pois = array();
    protected $_paths = array();
    
    protected $_upload_path = array(
         'image_itinerary' => 'image',
     );

    protected $_subformToSave = array(
         'image_itinerary' => 'Image_Itinerary' ,
    );

    protected function _edit()
    {
         try
        {
            Database::instance()->begin();
            
            $this->_orm->values($_POST)->save();
            
            //si eseguono le associazioni con i mezzi
            foreach(array('pois','paths','areas') as $alias)
            {
                $var = "_".$alias;
                if(isset($_POST[$alias]))
                    $this->$var = $_POST[$alias];
                $this->_orm->setManyToMany($alias,$this->$var);
            }
            
            $this->_save_subforms_1XN();
            
                
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
        $toRes = parent::_single_request_row($orm);
        
        // si aggiungo i codici ateco
        foreach(array('pois','paths','areas') as $alias)
        {
            $datas = $orm->$alias->find_all();
            foreach($datas as $data)
           {
               $toRes[$alias][] = array(
                   "id" => $data->id,
               );
           }
        }
         
        
        return $toRes;
    }
    
}