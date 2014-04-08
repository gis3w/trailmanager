<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Sheet_Base extends Controller_Ajax_Base_Crud{
    
    protected $_typologies = array();

    protected function _set_the_geom_edit()
    {
        
        $geodata  = json_decode($_POST['the_geom']);
   
        if(!empty($geodata->features))  
        {
            $fs = GEO::featurecollection2wkt($geodata);

            foreach($fs as $k => $f)
            {
                $reg = "(";
                $fs[$k] = strstr($f,$reg);
            }
            
            
            $parFrom = $parTo  = "";
            switch($this->_orm->geotype)
            {
                case "MULTILINESTRING":
                    $parFrom = "(";
                    $parTo = ")";
                break;
            }
            
            
            $_POST['the_geom'] = $this->_orm->geotype.$parFrom.  implode(",", $fs).$parTo;
        }
        else
        {
            //unset($_POST['the_geom']);
            $_POST['the_geom'] = NULL;
        }
            
        
        
    }
    
    protected function _set_typologies_edit()
    {
         //si eseguono le associazioni con i mezzi
            foreach(array('typologies') as $alias)
            {
                $var = "_".$alias;
                if(isset($_POST[$alias]))
                    $this->$var = $_POST[$alias];
                $this->_orm->setManyToMany($alias,$this->$var);
            }
    }
    
    protected function _data_edit()
    {
        $this->_set_the_geom_edit();
         $this->_orm->values($_POST);
//          print_r($this->_orm);
//         exit;
                 $this->_orm->save();
         
         $this->_set_typologies_edit();
    }

    protected function _edit()
    {
         try
        {
             
             //test per geo             
            Database::instance()->begin();

            $this->_data_edit();
                
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
        
        $this->_unset_ORMGIS_geofield($toRes);
        $this->_set_typologies($toRes, $orm);
        $this->_set_the_geom($toRes, $orm);
       
        return $toRes;
    }
    
    protected function _unset_ORMGIS_geofield(&$toRes)
    {
        unset(
                $toRes['the_geom'],
                $toRes['astext'],
                $toRes['asgeojson'],
                $toRes['box2d'],
                $toRes['centroid'],
                $toRes['asbinary'],
                $toRes['x'],
                $toRes['y']
                );
        
    }
    
    protected function _set_typologies(&$toRes ,$orm)
    {
        foreach(array('typologies') as $alias)
        {
            $datas = $orm->$alias->find_all();
            foreach($datas as $data)
           {
               $toRes[$alias][] = array(
                   "id" => $data->id,
               );
           }
        }
    }
    
    protected function _set_the_geom(&$toRes, $orm)
    {
        $toRes['the_geom'] = $orm->asgeojson_php;
    }
}