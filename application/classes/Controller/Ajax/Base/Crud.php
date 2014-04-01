<?php defined('SYSPATH') or die('No direct script access.');


abstract class Controller_Ajax_Base_Crud extends Controller_Ajax_Auth_Strict{
    
    const GET = 0;
    const INSERT = 1;
    const UPDATE = 2;
    const DELETE = 3;
    
    protected $_datastruct;
    protected $_datastructName;
    protected $_orm;
    protected $_typeOrm = 'ORM';
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


   public function before() {
       
       //si carica anche il datastruct per rsuperare altri dati se c'è e anche il resto a catena
       if(isset($this->_datastruct) AND class_exists("Datastruct_".$this->_datastruct,TRUE))
       {
           $this->_datastructName = strtolower($this->_datastruct);
           $this->_datastruct = Datastruct::factory($this->_datastruct);
           $this->_table  = $this->_datastruct->get_nameORM();
           $this->_typeOrm = $this->_datastruct->getTypeORM();
           
           
       }
       elseif(!isset($this->_table))
       {
           // si prende il modello dal controller
           $this->_table = Inflector::camelize($this->request->controller());
       }
       
        if(!isset($this->_table_rid))
               $this->_table_rid = strtolower ($this->_table);
       parent::before();
       
        // si controlla che il token da cui sta inviando sia quello giunto per i POST PUT AND DLETE
       // 25/07/2013 rimosso momentaneamente per provare il delete: aggiungere ,HTTP_Request::DELETE
//       if(in_array($this->_REST_Method,array(HTTP_Request::POST,HTTP_Request::PUT)))
//       {
//           $this->_session_token = $this->session->get('token');
//           if(!isset($_POST['csrf_token']))
//               throw HTTP_Exception::factory ('500',SAFE::message ('ehttp','500_no_csrf_token_submit'));
//           $this->_token = $_POST['csrf_token'];
//           if($this->_token !== $this->_session_token)
//               throw HTTP_Exception::factory ('500',SAFE::message ('ehttp','500_no_csrf_token_not_match'));
//       }
       
       // dati generali per l'upload
       $this->uploadPath = APPPATH."../".Controller_Download_Base::UPLOADPATH;
   }

   protected function _ACL()
    {
        $this->_controller_ACL();
        
        $typeORM = $this->_typeOrm;

        if(is_numeric($this->id))
        {
            $this->_status = $this->request->action() === 'update' ? self::UPDATE : self::DELETE;
             $this->_orm = $typeORM::factory($this->_table)
                     ->where($this->_table_rid.'.id','=',$this->id);
             $this->_apply_default_filter($this->_orm);
             $this->_orm = $this->_orm->find();

            //controllo della nullità della chiamata
            if($this->_orm->id === NULL AND !in_array($this->_table,array('Dpi_Mansione')))
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
         
           if(method_exists($this, '_set_environments'))
                    $this->_set_environments($row,$orm);
           
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
        $res = array();
       $rows = preg_split("/;/",$_POST[$field]);
       foreach($rows as $row)
       {
           if(!$row)
               continue;
           $subres = array();
           $data = preg_split("/,/", $row);
           foreach ($data as $d)
           {
               if(!$d)
                   continue;

               list($k,$v) = preg_split("/:/",$d);
                $subres[$k] = $v;
           }
           $res[] = $subres;
           
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
      
    protected function _join_for_azienda_user($orm)
    {
        if(!in_array($this->user->main_role_id,array(12,13)))
        {
            $orm->join('users_azienda')
                    ->on('users_azienda.azienda_id','=','azienda.id')
                    ->where('users_azienda.time_dissociazione','IS',DB::expr('null'))
                    ->where('users_azienda.user_id','=',$this->user->id);
        }
        else
        {
            $orm->group_by($this->_table_rid.'.id');
        }
             
    }
    
    protected function _get_validation_orm()
    {
         $this->_vorm = Validation::factory($_POST);
        
        // si aggiungono le rules per user
        foreach($this->_orm->rules() as $field => $rules)
            $this->_vorm->rules($field,$rules);
        
        $this->_vorm->labels($this->_orm->labels());
        
          if(!$this->_vorm->check())
            $this->vErrors = Arr::push ($this->vErrors,$this->_vorm->errors('validation'));
        
        if(!empty($this->vErrors))
                throw new Validation_Exception($this->_vorm);
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
