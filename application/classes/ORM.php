<?php defined('SYSPATH') OR die('No direct script access.');

class ORM extends Kohana_ORM {

    protected $_cache_get;
    
     protected function _initialize() {
        parent::_initialize();
        // si setta anche la primari key nel confgi del db
        $this->_db->setConfig('column_primary_key',$this->_primary_key);
    }
    
    public function save(Validation $validation = NULL) {
        $this->_db->setConfig('column_primary_key',$this->_primary_key);
        
        // si aggiunge il sistema di controllo per la lingua
        $lang = Session::instance()->get('lang');
        $lang_config = Kohana::$config->load('lang');
        $lang_default = $lang_config['default'];
        
        if($lang != $lang_default AND in_array($this->_table_name,$lang_config['tables_to_translate']))
        {
            // prima si deve eseguire il check
            $this->check($validation);
            // ora devo andare a salvare i valori presenti dentro la relativa tabella di traduzione
            foreach($this->changed() as $column =>$value)
            {
                $i18nData = $this->getTranslate($lang, $column,TRUE);
                $colLang = $lang."_val";
                if(!isset($i18nData->id))
                {
                    $i18nData->tb_id = $this->id;
                    $i18nData->tb = $this->_table_name;
                    $i18nData->col = $column;
                }
                $i18nData->$colLang = $_POST[$column];
                $i18nData->save();
            }
            return TRUE;
        }
        else
        {
            return parent::save($validation);
        }
        
        
        
    }
    
    
     public function get($column) {
       
         if(substr($column, 0,5) == 'orig_')
                 return parent::get(substr($column, 5));
         // per la internazionalizzazione recuperiamo il dato tradotto se c'è
         $lang = Session::instance()->get('lang');
         $lang_config = Kohana::$config->load('lang');
         $lang_default = $lang_config['default'];
         if($lang != $lang_default AND in_array($this->_table_name,$lang_config['tables_to_translate']))
         {
             // si controlla che la la colonna e la tabella ci siano nella tabella di traduzione per la lingua scelta
             // nel caso i assenza si invia il dato non tradotto
             $value = $this->getTranslate($lang,$column);
             return $value ? $value : parent::get($column);
         }
         else
         {
             return parent::get($column);
         }
        
    }
    
    public function getTranslate($lang,$column,$returnORM = FALSE)
    {
 
        $i18nData = ORM::factory('I18n')
                ->where('tb_id','=',(string)$this->orig_id)
                ->where('tb','=',$this->_table_name)
                ->where('col','=',$column)
                ->find();
        if($returnORM)
            return $i18nData;
        
        $colLang = $lang."_val";
        return $i18nData->$colLang;
    }
    

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
        
        $tbLog = $tbName.'_log';
        $tbId = $tbName.'_id';
        
        $log = ORM::factory($tbLog);
        $log->data_ins = time();
        $log->$tbId = $this->id;
        $log->user_id_mod = $user_modificatore->id;
        $log->azione = $action;
        $log->data = serialize($data);
        
        $log->save();
        
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
}