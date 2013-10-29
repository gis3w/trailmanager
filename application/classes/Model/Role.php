<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Role extends Model_Auth_Role {
    
    protected $_cache_capa = array();
    
    // Relationships
    protected $_has_many = array(
            'users' => array('model' => 'User','through' => 'roles_users'),
            'capabilities' => array('model' => 'Capability','through' => 'capabilities_roles'),
    );
    
    function get($column) {
        
        switch ($column)
        {
            case "array_name":
                $roles = $this->find_all();
                $role_names = array();
                foreach($roles as $role)
                    $role_names[] = $role->name;
                $value = $role_names;
            break;
            
            case "all_capabilities":
                $roles = $this->find_all();
                $capabilities = array();
                foreach($roles as $role)
                {
                    $role_capabilities = $role->capabilities->find_all();

                    foreach($role_capabilities as $role_capability)
                    {
                        if(!in_array($role_capability->name, $capabilities))
                            $capabilities[] = $role_capability->name;
                    }
                                                        
                
                }
                $value = $capabilities;
                    
            break;
            
            default:
                $value = parent::get($column);
        }
        
        return $value;
    }

    /**
    *
    * @param Arg $capabilies
    */
   public function allow_capa($capabilities)
   {     
       return  $this->_loaded ? $this->_allow_capa_single ($capabilities) : $this->_allow_capa_multi($capabilities);   
   }
   
   /**
    * Controlla che un tipo singolo di ruolo abbia le capabiliti indicate
    * @param array $capabilities
    * @return boolean
    */
   protected function _allow_capa_single($capabilities)
   {
       // se utente admin non ne tiene di conto
       if($this->name == 'ADMIN1')
               return TRUE;

       $capabilies = func_get_args();

       // si controlla nella cache
       if(!empty($this->_cache_capa))
       {
           foreach($capabilies as $capa)
           {
               if(isset($this->_cache_capa[$capa]) AND $this->_cache_capa[$capa] === TRUE)
                       return TRUE;
           }
       }

       $allowCapabilities = $this->capabilities->find_all();

       foreach($allowCapabilities as $capa)
       {
               if(in_array($capa->name,$capabilies))
               {
                       $this->_cache_capa[$capa->name] = TRUE;

                       return TRUE;
               }
       }

       return FALSE;
   }
   
   /**
    * Controlla che più ruoli abbiano le capacità richieste
    * @param array $capabilities
    * @return boolean
    */
   protected function _allow_capa_multi($capabilities)
   {
       // se utente admin non ne tiene di conto
       $roles = $this->array_name;
       if(in_array('ADMIN1',$roles))
               return TRUE;
       
       return FALSE;
   }
}
        
