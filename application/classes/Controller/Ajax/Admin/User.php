<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_User extends Controller_Ajax_Base_Crud{
    
    protected $_pagination = FALSE;

    protected $_datastruct = "User";
    
    protected $_unita_produttive = array();
    
    protected $_mansioni = array();
    
    protected $_aziende = array();
    
   


    protected function _get_data()
    {
 
        
        $ormStart = DB::select("users.id")
            ->from("users")
            ->join('user_datas','LEFT')
            ->on('user_datas.user_id','=','users.id')
            ->join('roles_users')
            ->on('roles_users.user_id','=','users.id')
             ->group_by("users.id");
        

        
        
            
        //$this->_join_for_azienda_user($orm);
        
        $this->_orm = $ormStart;
        
        return parent::_get_data();
    }
    
     protected function _get_list()
    {
        
        $ormStart = $this->_get_data();
        
        $orms = $this->_manage_orm_filter_page($ormStart,"execute");

       foreach($orms as $orm)
            $this->_build_res($this->_single_request_row(ORM::factory("User",$orm['id'])));

       $this->jres->data->items = array_values($this->_res);
    }
    
    
    
    protected function _edit() {
        
         try
        {
            Database::instance()->begin();

            $this->_validation_user();
            
            $this->_roles[] = ORM::factory ('Role')->where('name','=','login')->find()->id;
            
            if($this->_status === self::INSERT)
           {
               $_POST['data_ins'] = $_POST['data_mod'] = time();
           }
           else
           {
               $_POST['data_mod'] = time();
           }
           
           // aggiustamento della password
         
            $_tmp_post = array();
            foreach ($_POST as $k => $v)
            {
                if($v != '')
                    $_tmp_post[$k] = $v;
            }
            $_POST = $_tmp_post;
            
            if(isset($_POST['password']))
                        $this->_orm->data_first_change_password = NULL;
            
            // si passa al slvataggio vero e proprio
            $this->_orm->values($_POST)->save();
            
            // salvataggio degli user_data
            $user_data  = $this->_orm->user_data;
            
            $user_data->values($_POST);
                       
            if(!isset($user_data->user_id)) 
                $user_data->user_id = $this->_orm->id;
            
            $user_data->save();
           
           
                
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
    
    protected function _validation_user()
    {
        
        $this->_vorm = Validation::factory($_POST);
        
        // si aggiungono le rules per user
        foreach($this->_orm->rules() as $field => $rules)
            $this->_vorm->rules($field,$rules);
        
        // si aggiunge il controllo sul ruolo che deve essere obbligatorio
        $this->_vorm->rule('roles','not_empty');
        
        // si aggiunge la validazione della PASSWORD su PIN in base al tipo di utente
        if(isset($_POST['roles']) AND  is_array($_POST['roles']) ) 
               $this->_roles = $_POST['roles'];
        
        
         if(isset($_POST['username']) AND $_POST['username'] !== '' OR isset($_POST['password']) AND $_POST['password'] !== '' AND $_POST['password'] !== 'ZZZZZZZZZZ')
        {
            foreach($this->_orm->login_rules() as $field => $rules)
                $this->_vorm->rules($field,$rules);
            
            
                //$this->_vorm->rule('password','not_empty');
                $this->_vorm->rule('email','not_empty');
                $this->_vorm->rule('username','not_empty');
           
            
        }
 
               
          if(isset($_POST['password']) AND $_POST['password'] === 'ZZZZZZZZZZ')
                unset($_POST['password']);
           
        
        // si aggiungono le rules per user_data
        foreach($this->_orm->user_data->rules() as $field => $rules)
            $this->_vorm->rules($field,$rules);
        
        foreach($this->_orm->user_data->extra_rules() as $field => $rules)
            $this->_vorm->rules($field,$rules);
        
          if(!$this->_vorm->check())
            $this->vErrors = Arr::push ($this->vErrors,$this->_vorm->errors('validation'));
        
        if(!empty($this->vErrors))
                throw new Validation_Exception($this->_vorm);
        
    }
            
        protected function _single_request_row($user)
    {
         return self::user_data_plus($user);
    }
    
    /**
     * Recupera i dati accessori degli utenti e li impacche in un array
     * @param ORM $u
     * @return Array
     */
    public static function user_data_plus(ORM $u)
    {
        $udarr = Arr::to_arrayjres($u->user_data->as_array(), 'get_item', 'user_data');

        $uarr = Arr::to_arrayjres($u->as_array(), 'get_item','user');
              
        // per ogni utente si mettono tutti i ruoli separati da una virgola
        $uarr['roles'] = array_keys($u->roles->where('roles_users.role_id','!=',14)->find_all()->as_array('id'));
        // paramentri aggiuntivi da inviare
        $uarr['main_role_id'] = $u->main_role_id;     
                                                                                

        return Arr::push($uarr,$udarr);
    }

}