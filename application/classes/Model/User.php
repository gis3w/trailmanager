<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_User extends Model_Auth_User {

    protected $_has_one = array(
        'user_data' => array(),
    );
    
    protected $_has_many = array(
            'user_tokens' => array('model' => 'User_Token'),
            'roles'       => array('model' => 'Role', 'through' => 'roles_users'),
        'mansioni'=> array(
            'model' => 'Mansione',
            'through' => 'user_mansioni',
            'far_key' => 'mansione_id'
        ),
        'unita_produttive'=> array(
            'model' => 'Unita_Produttiva',
            'through' => 'users_unita_produttiva',
            'far_key' => 'unita_produttiva_id'
        ),
        'aziende'=> array(
            'model' => 'Azienda',
            'through' => 'users_azienda',
            'far_key' => 'azienda_id'
        ),
        'scadenze' => array(
            'model'   => 'Scadenze_Abilitazione',
        ),
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
            
            case "mansioni_attuali":
            case "array_mansioni_attuali_id":
                $value = parent::get('mansioni')->where($this->_has_many['mansioni']['through'].'.time_dissociazione','is',DB::expr('NULL'))->find_all();
                if($column === "array_mansioni_attuali_id")
                {
                    $toValue = array();
                    foreach($value as $mezzo)
                        $toValue[] = $mezzo->id;
                    
                    $value = $toValue;
                }
            break;
            
            case "unita_produttive_attuali":
            case "array_unita_produttive_attuali_id":
                $value = parent::get('unita_produttive')->where($this->_has_many['unita_produttive']['through'].'.time_dissociazione','is',DB::expr('NULL'))->find_all();
                if($column === "array_unita_produttive_attuali_id")
                {
                    $toValue = array();
                    foreach($value as $mezzo)
                        $toValue[] = $mezzo->id;
                    
                    $value = $toValue;
                }
            break;
            
            case "aziende_attuali":
            case "array_aziende_attuali_id":
            case "aziende_attuali_implode":
                $value = parent::get('aziende')->where($this->_has_many['aziende']['through'].'.time_dissociazione','is',DB::expr('NULL'))->find_all();
                if($column === "array_aziende_attuali_id" OR $column === "aziende_attuali_implode")
                {
                    $toValue = array();
                    foreach($value as $mezzo)
                        $toValue[] = $mezzo->id;
                    
                    if($column === "aziende_attuali_implode")
                        $toValue = implode (",", $toValue);
                    $value = $toValue;
                }
            break;

        
           
        
            default:
                $value = parent::get($column);
        }
        return $value;
        
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
    
    
    public function setUnitaProduttive($unita)
    {
       $actions = Filter::hasToManyActions($unita, $this->array_unita_produttive_attuali_id); 
       //// VERAMENTE IMPORTANTE E' L'ORDINE DI ASSOCIAZIONE DISSOCIAZIONE
       //// PRIMA DI DISSOCIA E POI SI ASSOCIA
       foreach($actions['toRemove'] as $id)
           $this->remove('unita_produttive',$id);
       
       foreach($actions['toAdd'] as $id)
           $this->add('unita_produttive',$id);
    }
    
    public function setAziende($azienda)
    {
       $actions = Filter::hasToManyActions($azienda, $this->array_aziende_attuali_id); 
       //// VERAMENTE IMPORTANTE E' L'ORDINE DI ASSOCIAZIONE DISSOCIAZIONE
       //// PRIMA DI DISSOCIA E POI SI ASSOCIA
       foreach($actions['toRemove'] as $id)
           $this->remove('aziende',$id);
       
       foreach($actions['toAdd'] as $id)
           $this->add('aziende',$id);
    }
    
   

  
    

    
    
} // End User Model