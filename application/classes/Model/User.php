<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User extends Model_Auth_User {

    protected $_has_one = array(
        'user_data' => array(),
    );
    
    protected $_has_many = array(
            'user_tokens' => array('model' => 'User_Token'),
            'roles'       => array('model' => 'Role', 'through' => 'roles_users'),
        
       
    );
    
    public function labels() {
        return array(
            "logins" => __("Logins"),
            "last_login" => __("Last login"),
            "password" => __("Password"),
        );
    }
    
    public function rules()
    {
        return array(
            'email' => array(
                //array('not_empty'),
                array('email'),
                array(array($this, 'unique'), array('email', ':value')),
            ),
        );
    }
    
    public function login_rules()
    {
        return array(
            'username' => array(
//                array('not_empty'),
                array('max_length', array(':value', 32)),
                array(array($this, 'unique'), array('username', ':value')),
            ),
            'password' => array(
//                array('not_empty'),
//                array('numeric'),
//                array('max_length', array(':value', 4)),
                array(array($this, 'unique'), array('password', ':value')),
            )
        );
    }
    
    public function get($column) {
        
                
         // nìmeccanisco di cache
         if(isset($this->_cache_get[$column]))
                 return $this->_cache_get[$column];
        
        switch($column)
        {
            
        
            case 'role':
            case 'main_role':
            case 'main_role_id':
                 $r = array();
                 if(!isset($this->_cache_get['role']))
                 {
                     $r = $this->roles->where('name','!=','login')->order_by('level','DESC')->find_all();
                     if(isset($r[0]))
                         $this->_cache_get['role'] = $r[0];
                 }
                 else
                 {
                     $r[0] = $this->_cache_get['role'];
                 }
                $value = NULL;
                switch($column)
                {
                    case 'role':
                        if(isset($r[0]))
                            $value = $r[0];
                    break;

                    case 'main_role':
                        if(isset($r[0]))
                            $value = $r[0]->name;
                    break;

                    case 'main_role_id':
                        if(isset($r[0]))
                            $value = $r[0]->id;
                    break;

                    break;
                }

                $this->_cache_get[$column] = $value;

            break;
                      
            case 'nome_cognome':
                $this->_cache_get[$column] = $value = $this->user_data->nome. ' '.$this->user_data->cognome;
            break;


            case 'last_login':
            case 'data_first_canghe_password':
                $value = parent::get($column);
                if(isset($value) AND $value !== '')
                {
                    $value = date(SAFE::date_mode(),$value);
                }
                else
                {
                    $value = '';
                }
            break;
            
         
          
        
            default:
                $value = parent::get($column);
        }
        return $value;
        
    }

    /**
     * Return kohana model rules array for registration form data
     * @return array
     */
    public function registration_rules()
    {
        return array(
            'username' => array(
                array('not_empty'),
                array('max_length', array(':value', 32)),
                array(array($this, 'unique'), array('username', ':value')),
            ),
            'password' => array(
                array('not_empty'),
            ),
            'confirm_password' => array(
                array('not_empty'),
                array('matches', array(':validation', ':field', 'password')),
            ),
            'email' => array(
                array('not_empty'),
            ),
        );
    }

    /**
     * Metodo privato per la costruzione di una hash univoca
     * @return string
     */
    public  function build_hash_registration()
    {

        while (TRUE)
        {
            // Create a random token
            $hash_registration = sha1(Text::random('alnum', 32));

            // Make sure the token does not already exist
            $count = DB::select('id')
                ->where('hash_registration', '=', $hash_registration)
                ->from($this->_table_name)
                ->execute($this->_db)
                ->count();
            if ($count === 0)
            {
                // A unique hash_inscription has been found
                return $hash_registration;
            }
        }
    }
       
    
  
    /**
     * Controlla se un utente ha un determinato role
     * @param type $role
     * @return type
     * @throws UnexpectedValueException
     */
    public function is_a($role)
    {

        // Get role object
        if ( ! $role instanceof Model_Role)
        {
                if(is_numeric($role))
                {
                    $role = ORM::factory('role',$role);
                }
                else
                {
                    $role = ORM::factory('role', array('name' => $role));
                }
                
        }

        // If object failed to load then throw exception
        if ( ! $role->loaded())
                throw new UnexpectedValueException('Tried to check for a role that did not exist.');

        // Return whether or not they have the role
        return (bool) $this->has('roles', $role);
    }
    
    
   public function getRoles($only_id = FALSE)
    {
        if(isset($this->_cache['roles'][$only_id]))
            return $this->_cache['roles'][$only_id];
        $roles = $this->roles->where('name', '!=','login')->find_all();
        if($only_id)
        {
            $toRet = array();
            foreach($roles as $role)
                $toRet[] = $role->id;
            $this->_cache['roles'][$only_id] = $toRet;
            return $toRet;
        }
        $this->_cache['roles'][$only_id] = $roles;
        return $roles;
    }
    
    public function get_allow_capabilities($as_id = FALSE,$filters = array())
    {
        if(isset($this->_cache['allow_capabilities'][serialize($filters)]))
            return $this->_cache['allow_capabilities'][serialize($filters)];
        $allowCapabilities = array();
        $roles = $this->getRoles();
         foreach($roles as $role)
         {
             $role_capabilities = $role->capabilities;
                if(!empty($filters))
                    foreach($filters as $filter)
                    $role_capabilities->where($filter[0],$filter[1],$filter[2]);
                                       

             $role_capabilities = $role_capabilities->find_all() ;
             foreach ($role_capabilities as $capability)
             if(!in_array($capability->name, $allowCapabilities))
                     $allowCapabilities[] = $as_id ? $capability->id : $capability->name;  
         }
             
         
        $this->_cache['allow_capabilities'][serialize($filters)] = $allowCapabilities;
        Kohana::$log->add(Log::DEBUG, print_r($allowCapabilities,true));
        return $allowCapabilities;
    }
    
    public function allow_capa($capabilities)
   {
       // se utente admin non ne tiene di conto
       if($this->main_role == 'ADMIN1')
               return TRUE;
       
       $capabilities = is_array($capabilities) ? $capabilities : array($capabilities);
       $check = array_intersect($capabilities, $this->get_allow_capabilities());
       return !empty($check);
   }
    
   

  
    

    
    
} // End User Model