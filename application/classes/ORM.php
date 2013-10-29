<?php defined('SYSPATH') OR die('No direct script access.');

class ORM extends Kohana_ORM {

    protected $_cache_get;
    
     /**
    * Tests if a unique key value exists in the database.
    * Valido per ogni ORM
    *
    * @param   mixed    the value to test
    * @param   string   field name
    * @return  boolean
    */
   public function unique_key_exists($value, $field = NULL)
   {
           if ($field === NULL)
           {
                   // Automatically determine field by looking at the value
                   $field = $this->unique_key($value);
           }

           return (bool) DB::select(array(DB::expr('count(*)'), 'total_count'))
                   ->from($this->_table_name)
                   ->where($field, '=', $value)
                   ->where($this->_primary_key, '!=', $this->pk())
                   ->execute($this->_db)
                   ->get('total_count');
   }

    
    /**
    * Metodo che conttrolla se il record è da fare update o no
    */
    public function to_update()
    {
        return $this->loaded() AND !empty($this->_changed);

    }

    /**
     * Metodo per determinare lo stato dell'orm
     */
    public function state()
    {
        if($this->loaded())
        {
            if(empty($this->_changed))
            {
                return 'not_change';
            }
            else
            {
                return 'to_update';
            }
        }
        else
        {
            return 'to_insert';
        }
    }
    
    public function labels()
    {
        return array(            
            "data_ins" => __("Insert date"),
            "data_mod" => __("Update date"),
            "file" => __("File"),
            "nome" => __("Name"),
            );
    }


    public function get($column) {
        
        switch($column)
        {
            
            case "data_scadenza":
            case "data_eseguita":
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
          
            
            case "data_ins":
            case "data_mod":
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
        
            case 'data_scadenza_ts':
            case 'data_eseguita_ts':
            case 'data_ins_ts':
            case 'data_mod_ts':
                $columtrue = substr($column,0,-3); 
                $value = parent::get($columtrue);
            break;
            
            default:
                   $value = parent::get($column);
        }
        return $value;
    }


    
    /**
     * Metodo generale per cosa loggare
     * @return Array
     */ 
    protected function _toLog()
    {
        return $this->as_array();
    }

        /**
    * Metodo che logga i cambiamenti della tabella
    */
    public function log()
    {
        //si recuper l'azione 
        $action = strtoupper(Request::current()->action());
        $user_modificatore = Auth::instance()->get_user();
        
        $data = $this->_toLog();
        
        $tbName = $this->_table_name;
        if($this->_table_names_plural)
            $tbName = Inflector::singular ($tbName);
        
        $tbLog = Inflector::underscore(ucwords(Inflector::humanize($tbName)));
        $tbLog = 'Log_'.ucfirst($tbLog);
        $tbId = $tbName.'_id';
        
        $log = ORM::factory($tbLog);
        $log->data_ins = time();
        $log->$tbId = $this->id;
        $log->user_id_mod = $user_modificatore->id;
        $log->azione = $action;
        $log->data = serialize($data);
        
        $log->save();
        
    }
    
    public function add($alias, $far_keys) {
        
        switch ($alias)
        {
            case "unita_produttive":
            case "aziende":
            case "users":
            case "mansioni":
            case "veicoli":
            case "macchine":
            case "mezzi_sollevamento":
            case "responsabili":
                switch($this->_table_name)
                {
                    case "veicolo":
                    case "mezzo_sollevamento":
                    case "macchina":
                    case "dati_sostanza_preparato_chimico":
                    case "users":
                    case "unita_produttiva":
                    case "sostanza_preparato_chimico":
                    case "azienda":
                        $far_keys = ($far_keys instanceof ORM) ? $far_keys->pk() : $far_keys;

                        $columns = array($this->_has_many[$alias]['foreign_key'], $this->_has_many[$alias]['far_key'],'time_associazione');
                        $foreign_key = $this->pk();

                        $query = DB::insert($this->_has_many[$alias]['through'], $columns);

                        foreach ( (array) $far_keys as $key)
                        {
                                $query->values(array($foreign_key, $key,  time()));
                        }
                        if(isset($this->_has_many[$alias]['through_pk']))
                                   $this->_db->setConfig('column_primary_key',$this->_has_many[$alias]['through_pk']);

                        $query->execute($this->_db);

                        if(isset($this->_has_many[$alias]['through_pk']))
                                $this->_db->setConfig('column_primary_key',$this->_primary_key);

                        $value = $this;
                    break;
                    
                    default:
                        $value = parent::add($alias, $far_keys);
                }
                
                
            break;
        
            default:
                $value = parent::add($alias, $far_keys);
        }
        return $value;
    }
    
      public function remove($alias,  $far_keys = NULL) {
        
        switch ($alias)
        {
            case "unita_produttive":
            case "aziende":
            case "users":
            case "mansioni":
            case "veicoli":
            case "macchine":
            case "mezzi_sollevamento":
            case "responsabili":
                switch($this->table_name())
                {
                    case "veicolo":
                    case "mezzo_sollevamento":
                    case "macchina":
                    case "dati_sostanza_preparato_chimico":
                    case "users":
                    case "unita_produttiva":
                    case "sostanza_preparato_chimico":
                    case "azienda":
                        $far_keys = ($far_keys instanceof ORM) ? $far_keys->pk() : $far_keys;

                        $query = DB::update($this->_has_many[$alias]['through'])
                                ->set(array('time_dissociazione'=>time()))
                                ->where($this->_has_many[$alias]['foreign_key'], '=', $this->pk())
                                ->where($this->_has_many[$alias]['through'].'.time_dissociazione','is',DB::expr('NULL'));

                        if ($far_keys !== NULL)
                        {
                                // Remove all the relationships in the array
                                $query->where($this->_has_many[$alias]['far_key'], 'IN', (array) $far_keys);
                        }

                        if(isset($this->_has_many[$alias]['through_pk']))
                         $this->_db->setConfig('column_primary_key',$this->_has_many[$alias]['through_pk']);

                        $query->execute($this->_db);

                         if(isset($this->_has_many[$alias]['through_pk']))
                            $this->_db->setConfig('column_primary_key',$this->_has_many[$alias]['through_pk']);

                         $value = $this;
                    break;
                
                    default:
                        $value = parent::remove($alias, $far_keys);
                }                
            break;
        
            default:
                $value = parent::remove($alias, $far_keys);
        }
        return $value;
    }
    
    public function descriptions() {
        return array();
    }
    
    /**
     *  Recupera un array di id dei valori molti a molti per l'alias specificato
     * @param string $alias
     * @return array
     */
    public function getArrayIdHasMany($alias)
    {
        $value = array();
        $array = $this->$alias->find_all();
        foreach ($array as $data)
            $value[] = $data->id;
        return $value;
    }
    
    /**
     * Metofo per salvare in maniera automatia le relazioni molti a molti
     * @param string $alias
     * @param array $values
     */
    public function setManyToMany($alias, array $values)
    {
        $actions = Filter::hasToManyActions($values, $this->getArrayIdHasMany($alias));

        foreach($actions['toRemove'] as $id)
            $this->remove($alias,$id);
        foreach($actions['toAdd'] as $id)
            $this->add($alias,$id);
    }
    
    //// metodi utilizzabili da tutti o quasi tutti quelli che hanno associazioni ///
    
    public function setManyToManyAssociation($field,$data)
   {
        $actual_data_key = "array_".$field."_attuali_id";
        $actions = Filter::hasToManyActions($data, $this->$actual_data_key); 
       //// VERAMENTE IMPORTANTE E' L'ORDINE DI ASSOCIAZIONE DISSOCIAZIONE
       //// PRIMA DI DISSOCIA E POI SI ASSOCIA
       foreach($actions['toRemove'] as $id)
           $this->remove($field,$id);
       
       foreach($actions['toAdd'] as $id)
           $this->add($field,$id);
    }
    
    public function setOneAssociation($field,$key,$data)
   {
        
        if($data == $this->$key->id)
            return FALSE;
        
           $this->remove($field,$this->$key->id);
           if(isset($data) AND $data !== '')
                $this->add($field,$data);
    }
    
    
    public function setUsers($utenti)
    {
       $this->setManyToManyAssociation('users', $utenti);
    }
    
    public function setVeicoli($veicoli)
    {
       $this->setManyToManyAssociation('veicoli', $veicoli);
    }
    
     public function setMacchine($macchine)
    {
       $this->setManyToManyAssociation('macchine', $macchine);
    }
    
     public function setMezziSollevamento($mezzi_sollevamento)
    {
       $this->setManyToManyAssociation('mezzi_sollevamento', $mezzi_sollevamento);
    }
    
    public function setMansioni($mansioni)
    {
       $this->setManyToManyAssociation('mansioni', $mansioni);
    }
    
    public function setAzienda($azienda_id)
    {
        $this->setOneAssociation('aziende', 'azienda_attuale', $azienda_id);
    }
    
    public function setResponsabile($responsabile_id)
    {
        $this->setOneAssociation('responsabili', 'responsabile_attuale', $responsabile_id);
    }
    
    public function setManyAzienda($aziende)
    {
        $this->setManyToManyAssociation('aziende', $aziende);
    }
}