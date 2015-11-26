<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Sheet_Base extends Controller_Ajax_Base_Crud{
    
    protected $_typologies = array();

    protected $_itineraries = array();
    
     protected $_upload_path = array(
         'image_poi' => 'image',
         'image_path' => 'image',
         'image_area' => 'image',
     );
     
     protected  $_subformToSave = array(
                'video_poi' => 'Video_Poi' ,
                'video_path' => 'Video_Path',
                'video_area' => 'Video_Area',
                'image_poi' => 'Image_Poi',
                'image_path' => 'Image_Path',
                'image_area' => 'Image_Area'
    );

    protected $_noValidation = [
        'image_path'
    ];
     
    protected $_url_multifield_postname;
    protected $_url_multifiled_value;
    protected $_url_multifield_nameORM;
    protected $_url_multifield_foreignkey;



    protected function _set_the_geom_edit()
    {
        if(!isset($_POST['the_geom']))
        {
            $_POST['the_geom'] = NULL;
            return;
        }

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
                case "MULTIPOLYGON":
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
            foreach(array('typologies') as $alias)
            {
                $var = "_".$alias;
                if(isset($_POST[$alias]))
                    $this->$var = $_POST[$alias];
                $this->_orm->setManyToMany($alias,$this->$var);
            }
    }

    protected function _set_itineraries_edit()
    {
        foreach(array('itineraries') as $alias)
        {
            $var = "_".$alias;
            if(isset($_POST[$alias]))
                $this->$var = $_POST[$alias];
            $this->_orm->setManyToMany($alias,$this->$var);
        }
    }
    
    protected function _data_edit()
    {
        Filter::emptyPostDataToNULL();
        unset($_POST['data_mod'],$_POST['data_ins']);
        $this->_set_the_geom_edit();
        $this->_orm->values($_POST);

        if(isset($this->_orm->{$this->_orm->primary_key()}))
        {
            $this->_orm->data_mod = time();
        }
        else
        {
            $this->_orm->data_ins = $this->_orm->data_mod = time();
        }

        $this->_orm->save();

        if(isset($this->_orm->typologies))
            $this->_set_typologies_edit();

        if(isset($this->_orm->itineraries))
            $this->_set_itineraries_edit();
         
         $this->_save_subforms_1XN();
         
         $this->_save_url_multifiled();
                  
    }

    
    /**
     * Save th eurl motifield values
     */
    protected function _save_url_multifiled()
    {
        // si salvano gli elementi del sub form
            if(isset($_POST[$this->_url_multifield_postname]))
                if(!isset($this->_url_multifiled_value))
                    $this->_url_multifiled_value = $this->_get_subform_multifield_data($this->_url_multifield_postname);
            
             if(!empty($this->_url_multifiled_value))
            {
                foreach($this->_url_multifiled_value as $url)
                {  
                    if(!isset($url['stato']))
                        continue;
                    
                    // si forza unita_produttiva_id anche per lìinserimento
                    $url[$this->_url_multifield_foreignkey] = $this->_orm->id;
                    $id = (isset($url['id']) AND $url['id'] != '') ? $url['id'] : NULL;
                    $oUrl = ORM::factory($this->_url_multifield_nameORM,$id);
                    if($url['stato'] == 'I' OR $url['stato'] == 'U')
                        $oUrl->values($url)->save();
                    if($url['stato'] == 'D')
                        $oUrl->delete();
                 }
            }

        
    }
    
    protected function _validation_url_multifiled()
    {
        // oltre alla non empty di dpi e mansioni è necessario
        // validare gli indroci per unità produttiva che non si devono sovrapporre ??? chiedere
        
         // si implementa la validazione del multifield
        if(isset($_POST[$this->_url_multifield_postname]))
        {
            $this->_url_multifiled_value = $this->_get_subform_multifield_data($this->_url_multifield_postname);
        
             if(isset($this->_url_multifiled_value))
                // si aggiungono fittiziamente al post per poi fare la validazione
                foreach ($this->_url_multifiled_value as $nRow => $url)
                {
                   $_POST['url-row'.$nRow] = $url['url'];
                   $_POST['alias-row'.$nRow] = $url['alias'];
                   $_POST['description_url-row'.$nRow] = $url['description_url'];
                }
            
        }

            $this->_vorm = Validation::factory($_POST);
            
            // si aggiungono le validazioni dell'orm
            foreach ($this->_orm->rules() as $col => $rule)
                $this->_vorm->rules($col, $rule);

            if ($this->_status == self::INSERT and method_exists($this->_orm, 'insert_rules'))
                foreach ($this->_orm->insert_rules() as $col => $rule)
                    $this->_vorm->rules($col, $rule);



        // si aggiungono anche le labels
           $this->_vorm->labels($this->_orm->labels());

            if(isset($this->_url_multifiled_value))
                 // si aggiungono le regoloe fittizie per il multifield
                foreach ($this->_url_multifiled_value as $nRow => $url)
                {
                  if($url['stato'] == 'D')
                      continue;
                  
                  $uOrm = ORM::factory($this->_url_multifield_nameORM);
                  foreach ($uOrm->rules() as $col => $rule)
                    $this->_vorm->rules( $col.'-row'.$nRow, $rule);   

                }

            if(isset($_POST[$this->_url_multifield_postname]))
                $this->_vorm->label($_POST[$this->_url_multifield_postname],__('Urls'));
            
            //adding empty image validation
            /*
            $imageField = 'image_'.strtolower($this->_datastruct->get_nameOrm());
            if(!in_array($imageField,$this->_noValidation))
            {
                $this->_vorm->rule($imageField, 'not_empty');
                $this->_vorm->label($imageField,__('Images to upload'));
            }
            */
       
        
        
          if(!$this->_vorm->check())
            $this->vErrors = Arr::push ($this->vErrors,$this->_vorm->errors('validation'));
        
        if(!empty($this->vErrors))
                throw new Validation_Exception($this->_vorm);
        
    }
    


    protected function _edit()
    {
         try
        {             
             //test per geo             
            Database::instance()->begin();
            
            $this->_validation_url_multifiled();

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
        $this->_set_itineraries($toRes,$orm);
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
            if(!isset($orm->$alias))
                continue;
            $datas = $orm->$alias->find_all();
            foreach($datas as $data)
           {
               $toRes[$alias][] = array(
                   "id" => $data->id,
               );
           }
        }
    }

    protected function _set_itineraries(&$toRes ,$orm)
    {
        foreach(array('itineraries') as $alias)
        {
            if(!isset($orm->$alias))
                continue;
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
        if($orm instanceof ORMGIS)
            $toRes['the_geom'] = $orm->asgeojson_php;
    }
    

}