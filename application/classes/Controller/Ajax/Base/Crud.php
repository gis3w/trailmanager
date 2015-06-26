<?php defined('SYSPATH') or die('No direct script access.');


abstract class Controller_Ajax_Base_Crud extends Controller_Ajax_Auth_Strict{
    
    const GET = 0;
    const INSERT = 1;
    const UPDATE = 2;
    const DELETE = 3;
    
    protected $_datastruct;
    protected $_datastructName;
    protected $_primary_column = 'id';
    protected $_orm;
    protected $_typeORM = 'ORM';
    protected $_table;
   protected $_table_rid;
   protected $_res = array();
   protected $_vorm;
   protected $vErrors = array();
   protected $_status = self::GET;
   protected $_session_token;
   protected $_token;
   public $uploadPath;
   protected $_extra_validation;
       
   protected $_history = array();
   
   protected $_environments;
   
   public $translated;
   
   protected $_subformToSave= array();
   protected $_upload_path = array();


   public function before() {

       //si carica anche il datastruct per rsuperare altri dati se c'è e anche il resto a catena
       if(isset($this->_datastruct) AND class_exists("Datastruct_".$this->_datastruct,TRUE))
       {
           $this->_datastructName = strtolower($this->_datastruct);
           $datastruct = $this->_datastruct = Datastruct::factory($this->_datastruct);
           $this->_table  = $this->_datastruct->get_nameORM();
           $this->_typeORM = $this->_datastruct->getTypeORM();
           $this->_primary_column = $this->_datastruct->primary_key;
           
           // si filtrano i dati post se ci un prekeyfield
           if(isset($datastruct::$preKeyField))
               foreach($_POST as $k => $v)
                   if(substr_compare($k, $datastruct::$preKeyField, 0, strlen($datastruct::$preKeyField))=== 0)
                   {
                       $newK = substr($k, strlen($datastruct::$preKeyField) + 1);
                       $_POST[$newK] = $v;
                        unset($_POST[$k]);
                   }
 
               
           
       }
       elseif(!isset($this->_table))
       {
           // si prende il modello dal controller
           //$this->_table =  Inflector::camelize($this->request->controller());
           $this->_table =  Text::ucfirst($this->request->controller(),'_');
       }
       
        if(!isset($this->_table_rid))
               $this->_table_rid = strtolower ($this->_table);
       parent::before();
       
        // si controlla che il token da cui sta inviando sia quello giunto per i POST PUT AND DLETE
    //       if(in_array($this->_REST_Method,array(HTTP_Request::POST,HTTP_Request::PUT)))
    //       {
    //           $this->_session_token = $this->session->get('token');
    //           if(!isset($_POST['csrf_token']))
    //               throw HTTP_Exception::factory ('500',SAFE::message ('ehttp','500_no_csrf_token_submit'));
    //           $this->_token = $_POST['csrf_token'];
    //           if($this->_token !== $this->_session_token)
    //               throw HTTP_Exception::factory ('500',SAFE::message ('ehttp','500_no_csrf_token_not_match'));
    //       }
    //       
       // dati generali per l'upload
       $this->_upload_path_base = Controller_Download_Base::UPLOADPATH;
        foreach($this->_upload_path as $type => $path)
            $this->_upload_path[$type] = $this->_upload_path_base."/".$path."/";
   }

   protected function _ACL()
    {
        $this->_controller_ACL();
        
        $this->_get_orm_base();
        
        
    }
    
    protected function _get_orm_base()
    {
        $typeORM = $this->_typeORM;
        
        if(is_numeric($this->id))
        {
            $this->_status = $this->request->action() === 'update' ? self::UPDATE : self::DELETE;
             $this->_orm = $typeORM::factory($this->_table)
                     ->where($this->_table_rid.'.'.$this->_primary_column,'=',$this->id);
             $this->_apply_default_filter($this->_orm);
             $this->_orm = $this->_orm->find();

            //controllo della nullità della chiamata
            if($this->_orm->pk() === NULL)
            {
                throw new HTTP_Exception_500(SAFE::message('ehttp','500_'.$this->_table_rid.'_id'));
            }
        }
        else
        {
            
             $this->_orm = $typeORM::factory($this->_table);
             $this->_apply_default_filter($this->_orm);
            // caso di inserimento
            if($this->request->action() === 'create'){
                $this->_status = self::INSERT;
            }
        }
    }




    protected function _get_data()
   {
       $orm = $this->_orm;

       if($this->id === 'list')
            $this->_apply_default_filters($orm);
       
       return $orm;
       
   }
   
   protected function _get_item() {

        $this->jres->data->tot_items = 1;
        $this->jres->data->page = 1;
        $this->jres->data->offset = 0;
        $this->jres->data->items_per_page = 1;
        $this->jres->data->items = array(
            $this->_single_request_row($this->_get_data())
        );
        
        $this->_get_history();
   }

   protected function _get_list()
    {
        
        $ormStart = $this->_get_data();
        
        $orms = $this->_manage_orm_filter_page($ormStart);

       foreach($orms as $orm)
            $this->_build_res($this->_single_request_row($orm));

       $this->jres->data->items = array_values($this->_res);
    }
    
    protected function _single_request_row($orm)
    {
        
         $row = $orm->as_array();
        //se la lingua è differente da quella di default
        
        // si aggiunge il sistema di controllo per la lingua
        $lang = Session::instance()->get('lang');
        $lang_config = Kohana::$config->load('lang');
        $lang_default = $lang_config['default'];
        $colLang = $lang."_val";
        if($lang != $lang_default AND in_array($orm->table_name(),$lang_config['tables_to_translate']))
        {
             $row['traslated_columns'] = array();
             $colsTraslated = ORM::factory('I18n')
                ->where('tb','=',$orm->table_name())
                ->where('tb_id','=',(string)$orm->id)
                ->where($colLang,'IS NOT',DB::expr('NULL'))
                ->find_all();
             
             foreach($colsTraslated as $colTraslated)
                 $row['traslated_columns'][] = $colTraslated->col;
        }
        
            $datastruct = $this->_datastruct;
            if(isset($datastruct) AND isset($datastruct::$preKeyField))
            {
                foreach($row as $k => $v)
                {
                    $newK = $datastruct::$preKeyField.'-'.$k;
                    $row[$newK] = $v;
                    unset($row[$k]);
                }
            }
           
           return $row;
    }

    protected function _build_res($orm)
    {
        $this->_res[] = $orm;
    }    
    
    public function action_create()
    {
        $this->_edit();
    }
    
     public function action_update()
    {
        $this->_edit();
    }




    protected function _base_edit()
    {
         try
        {
            Database::instance()->begin();
            
            $this->_get_extra_validation();
            
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



    protected function _edit()
    {
      $this->_base_edit();
    }
    
    
    public function action_delete()
    {
        try
        {
            Database::instance()->begin();
            
            $this->_orm->delete();
            
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
    
    /**
     *  Metodo per il recuper eil parsing del i dati serializzati provenienti da un subform
     * @param string $field
     * @return array
     */
    protected function _get_subform_data($field)
    {
        // si controlla se esiste il datastruct per questo subform
        $className = Inflector::underscore(ucwords(Inflector::humanize($field)));
        if(class_exists('Datastruct_'.$className,TRUE))
            $datastruct = Datastruct::factory($className);
        
        $res = array();
       $rows = preg_split("/;/",$_POST[$field]);
       foreach($rows as $row)
       {
           $rowArr = array();
           if(!$row)
               continue;
           
           parse_str($row,$rowArr);         
           if(isset($datastruct))
           {
               $res[]= $this->_removePreKeyFieldFromData($rowArr,$datastruct);
           }
           else
           {
               $res[] = $rowArr;
           }
                       
           
       }

        return $res;
    }
    
    /**
     * Get data from a serialized multifield datastruct field
     * @param type $field
     * @return array
     */
    protected function _get_subform_multifield_data($field)
    {        
        $res = array();
       $rows = preg_split("/;/",$_POST[$field]);
       foreach($rows as $row)
       {
           $rowArr = array();
           if(!$row)
               continue;
           
           parse_str($row,$rowArr);           
           $res[]= $rowArr;           
           
       }

        return $res;
    }
    
//    /**
//     * Metodo per l'aggiunta delle regole di validazione dei sub form
//     * @param array $data
//     * @param string $ormname
//     */
//     protected function _validation_subform_data($data,$ormname)
//    {
//        $rules = ORM::factory($ormname)->rules();  
//       
//        
//        foreach ($data as $d)
//        {
//            foreach($rules as $field => $rule)
//            {
//                $this->_vorm->rules($field."-tk".$d['token'],$rule);
//            }
//        }
//    }
    
     protected function _get_validation()
    {
         try
        {
            Database::instance()->begin();
            
            // dobbiamo implementare la validazione
            var_dump('IMPLEMENTARE IL PROCESSO DI VALIDAZIONE');
            exit;
            
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
    
    protected function _get_extra_validation()
    {
           $this->_extra_validation = NULL;
            if(method_exists($this->_orm, 'extra_rules'))
            {
                $this->_extra_validation = Validation::factory($_POST);
                foreach($this->_orm->extra_rules() as $field => $rules)
                    $this->_extra_validation->rules($field,$rules);
                   
            }
    }
    
    protected function _save_subform_document_data($datastruct, $ormName,$foreign_key,$far_key,$subuploadpath)
    {
        $subformdata = $this->_get_subform_data($datastruct);
            
            // passata la validazione e il salvataggio vado a salvare 
            foreach($subformdata as $doc)
            {                
                if(!isset($doc['stato']))
                    continue;
                
                SAFE::setDatainsDatamod($doc);
                $doc['nome'] = $doc['file'];

                switch($doc['stato'])
                {
                    case "I":
                        $rac = ORM::factory($ormName)->values($doc);
                        $rac->save();
                        $this->_orm->add('documenti',$rac);
                    break;

                    case "U":
                        $rac = $this->_orm->documenti->where($foreign_key,'=',$doc['id'])->find();
                        if($rac->nome !== $doc['nome'])
                            $path_to_delete = $this->uploadPath."/".$subuploadpath."/".$rac->$far_key."/".$rac->nome;
                        $rac->values($doc)->save();
                        @unlink($path_to_delete);
                    break;

                    case "D":
                        $rac = $this->_orm->documenti->where($foreign_key,'=',$doc['id'])->find();
                        $path_to_delete = $this->uploadPath."/".$subuploadpath."/".$rac->$far_key."/".$rac->nome;
                        error_log($path_to_delete);
                        $rac->delete();
                        @unlink($path_to_delete);
                    break;
                }
            }
            
    }
    
    protected function _save_subforms_1XN()
    {
            foreach($this->_subformToSave as $name_subform => $name_orm)
           {
                if(isset($_POST[$name_subform]))
                {
                   $subformDatas = $this->_get_subform_data($name_subform);
                   if(!empty($subformDatas))
                   {
                       foreach($subformDatas as $norder => $sfd)
                       {  
                          
                           // si forza unita_produttiva_id anche per lìinserimento
                           $sfd[$this->_table_rid.'_id'] = $this->_orm->id;
                           $id = isset($sfd['id']) ? $sfd['id'] : NULL;
                           $subformOrm = ORM::factory($name_orm,$id);
                           $subformOrm->norder = $norder;
                            if(!isset($sfd['stato']))
                               $sfd['stato'] = NULL;
                           switch($sfd['stato'])
                           {
                               case "I":
                               case "U":
                                   if($sfd['stato'] == 'I')
                                   {
                                       $sfd['data_mod'] =$sfd['data_ins'] = time();
                                   }
                                   else
                                   {
                                       $sfd['data_mod'] = time();
                                       unset($sfd['data_ins']);
                                       // si tenta leliminazione del precedente
                                       if(isset($subformOrm->file) AND isset($this->_upload_path[$name_subform]) AND $subformOrm->file != $sfd['file'])
                                        {
                                            $path_to_delete = APPPATH."../".$this->_upload_path[$name_subform].$subformOrm->file;
                                             @unlink($path_to_delete);
                                             // anche le thimbnails
                                            $path_to_delete_thimb = APPPATH."../".$this->_upload_path[$name_subform].'thumbnail/'.$subformOrm->file;
                                            @unlink($path_to_delete_thimb);
                                        }
                                   }
                                   $subformOrm->values($sfd)->save();
                               break;

                               case "D":
                                   if(isset($subformOrm->file))
                                        $fileToDelete = $subformOrm->file;
                                   $subformOrm->delete();
                                    if(isset($fileToDelete) and isset($this->_upload_path[$name_subform]))
                                   {
                                       $path_to_delete = APPPATH."../".$this->_upload_path[$name_subform].$fileToDelete;
                                        @unlink($path_to_delete);
                                        // anche le thimbnails
                                        $path_to_delete_thimb = APPPATH."../".$this->_upload_path[$name_subform].'thumbnail/'.$fileToDelete;
                                        @unlink($path_to_delete_thimb);
                                   }
                                  
                                   
                               break;
                               
                               // caso in cui non ci sia lo stato ma si modifica l'ordine
                               default:
                                   if(in_array('norder', array_keys($subformOrm->table_columns())))
                                   {
                                       $subformOrm->norder = $norder;
                                       $subformOrm->save();
                                   }
                                   
                           }

                   }
               } 
           }
       }
    }
    



    protected function _get_history_parent($node,$orm_node = NULL)
    {
        $parent = $node->parent;
        if(!$parent)
            return;
        
        $parent_orm_key = $node->parent_orm_key;
        $orm_node = isset($orm_node) ? $orm_node->$parent_orm_key : $this->_orm->$parent_orm_key;

        $this->_history[] = array('datastruct' => $parent->datastruct,'id' => $orm_node->id);
        if($parent->parent)
            $this->_get_history_parent ($parent, $orm_node);
        
    }


    protected function _get_history()
    {
        $history = array();

        $node = $this->datagram->find($this->_datastructName);
        if(!$node)
            return;
        
        $this->_get_history_parent($node);
       
        $history = array_reverse($this->_history);
        $this->jres->data->items[0]['history'] = $history;
    }
    
    protected function setMayToMayAssociation($key,$method = NULL)
    {
        if(!$method)
            $method = "set".ucfirst ($key);
        
        $pKey = "_".$key;
        
        if(isset($_POST[$key]))
                $this->$pKey = $_POST[$key];
            $this->_orm->$method($this->$pKey);
    }
      
    
    protected function _get_validation_orm()
    {
        // si deve ripulire il post dai prekeyfiled
        $this->_removePreKeyFieldFromPost();
         $this->_vorm = Validation::factory($_POST);
        
        // si aggiungono le rules per user
        foreach($this->_orm->rules() as $field => $rules)
            $this->_vorm->rules($field,$rules);
        
        $this->_vorm->labels($this->_orm->labels());
        
          if(!$this->_vorm->check())
            $this->vErrors = Arr::push ($this->vErrors,$this->_vorm->errors('validation'));
        
        if(!empty($this->vErrors))
        {
            $this->_preKeyFiled2errors();
            throw new Validation_Exception($this->_vorm);
        }
                
    }
    
    protected function _preKeyFiled2errors($datastruct = NULL)
    {
        $datastruct = isset($datastruct) ? $datastruct : $this->_datastruct;
        if(isset($datastruct) AND isset($datastruct::$preKeyField))
            foreach($this->vErrors as  $key => $error)
            {
                $newKey = $datastruct::$preKeyField.'-'.$key;
                $this->vErrors[$newKey] = $error;
                unset($this->vErrors[$key]);
            }
    }
    
    protected function _removePreKeyFieldFromData($data,$datastruct = NULL)
    {
        $datastruct = isset($datastruct) ? $datastruct : $this->_datastruct;
        if(isset($datastruct) AND isset($datastruct::$preKeyField))
           {
                foreach($data as $k => $v)
                   if(substr_compare($k, $datastruct::$preKeyField, 0, strlen($datastruct::$preKeyField))=== 0)
                   {
                       $newK = substr($k, strlen($datastruct::$preKeyField) + 1);
                       $data[$newK] = $v;
                        unset($data[$k]);
                   }
           }
       return $data;
    }
    
    protected function _removePreKeyFieldFromPost()
    {
        $_POST = $this->_removePreKeyFieldFromData($_POST);
    }


    protected function _validation_orm()
    {
         try
        {
            $this->_get_validation_orm();

        }
        catch (ORM_Validation_Exception $e)
        {            
            $this->_validation_error($e);
        }
        catch (Validation_Exception $e)
        {            
            $this->_validation_error($this->vErrors);
            
        }
    }
   
}
