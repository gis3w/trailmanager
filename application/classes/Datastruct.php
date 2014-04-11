<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct extends Kohana_Formstruct{
    
    public $enctype;
    
    public $title;
    
    public $user;
    
    protected $_nameORM;
        
    protected $_typeORM = 'ORM';

    protected $_baseORM;
    
    protected $_labelsORM;

    protected $_descriptionsORM;
    
    public $filter = FALSE;
    
    public $lang;
    public $lang_default;

    /**
     * Le colonne del form
     * @var Array
     */
    protected $_columns = array();
    
    /**
     * Le colonne e cmapi  che veranno filtrati a second acl e altro
     * @var array
     */
    protected $_columns_to_remove = array();


    protected $_order_to_render;
    
    protected $fields_to_save;
    
    public $capabilities;
    
    protected $_capablity_name;
    
    

    // i gruppi in cui sono suddivisi i form
    public $groups;
    
    public $menu;
    
    const SUBFORM = 'subform';
    
    // modalità di recuper default value per le combobox
    // per il campo default_value
    const DEFAULT_VALUE_URL = 'url';    // recuperare dall'url
    const DEFAULT_VALUE_DATA = 'data'; //i valori ci sono gia in queli caricati ma mancano gli altri
    const DEFAULT_VALUE_DATA_ID = 'data-id'; // nonpossiedo il valore di default
    
    // costanti di tipologia di capabilities
    const CAPA_LIST = 'list';
    const CAPA_INSERT = 'insert';
    const CAPA_UPDATE = 'update';
    const CAPA_DELETE = 'delete';
    


    protected $_columnStruct = array(
        "data_type" => "character varying",
        "editable" => TRUE,
        "form_input_type" => self::INPUT,
        "form_show" => TRUE,
        "subform_table_show" => TRUE,   // indica se deve essere visualizzato al livello della tabella subform
        "table_show" => TRUE, // se deve essere visualizzatonella tabella principale
    );
    
    protected $_menuItemStruct = array(
        "label" => "",
        "url" => "",
        "params" => array()
    );

    protected function __construct() {
            
        $this->user = Auth::instance()->get_user();
        $this->_initialize();

    }
    
    protected function _initialize()
    {
        // tipo fi form
        if(!isset($this->enctype))
            $this->enctype = self::ECNTYPE_DEFAULT;
        
            
        
        if(!isset($this->_nameORM))
            $this->_nameORM = substr(get_class($this), 11);
        
        // si setta la lingua
        $this->_set_lang();
        
        $this->_baseORM = ORM::factory($this->_nameORM);
        $this->_labelsORM = $this->_baseORM->labels();
        $this->_descriptionsORM =$this->_baseORM->descriptions();
        
        // si inizializza il gruppo principale
        $this->_get_orm_columns();
        
        //si inizialza i menu se ci sono
        $this->_get_menu_items();
        
        $this->_set_capabilities();
         
        //si impostano anche i campi da salvare
        $this->_get_fields_to_save();
    }
    
    protected function _set_lang()
    {
        $this->lang = Session::instance()->get('lang');
        $lang_config = Kohana::$config->load('lang');
        $this->lang_default = $lang_config['default'];
    }


    protected function _get_fields_to_save()
    {
        $toRet = array('id');
        $this->fields_to_save = array_merge($toRet,$this->_fields_to_save());     
    }


    public static function factory($nameORM)
    {
        $class = "Datastruct_".$nameORM;
        return new $class();
    }
    
    protected function _get_menu_items()
    {
        if(method_exists($this, '_menu_items'))
            $this->menu = $this->_menu_items();

        if($this->user->main_role_id !== '12' AND !is_null($this->menu))
            $this->_filter_by_capability($this->menu);
               
    }

    protected function _get_orm_columns()
    {
        $table_columns = $this->_baseORM->table_columns();
        $_columns_type = $this->_columns_type();
        $_extra_columns_type = $this->_extra_columns_type();
        
        foreach($table_columns as $name => $colData)
        {            
            $_column = $this->_columnStruct;
            $_column['label'] = isset($this->_labelsORM[$name]) ? $this->_labelsORM[$name] : ucfirst($name);
            if(isset($this->_descriptionsORM[$name]))
                $_column['description'] = $this->_descriptionsORM[$name];
            $_column = array_replace($_column, array_intersect_key($colData, $_column));
            $_column_type = isset($_columns_type[$name]) ? $_columns_type[$name]: array();
            
            if(in_array($name,array('id','gid')))
                    $_column['editable'] = FALSE;

            $_column = array_replace($_column, $_column_type);

            $this->_columns[$name] = $_column;           
        }
        
        $this->_columns = array_replace($this->_columns, $this->_foreign_column_type());
        // si aggiungon le colonne extra se si cono
        $this->_columns += $this->_extra_columns_type();
        
        if($this->user->main_role_id !== '12' AND !is_null($this->_columns))
            $this->_filter_by_role($this->_columns);
    }
    
    public function getTypeORM()
    {
        return $this->_typeORM;
    }
    
    public function render()
    {
        parent::render();
        
        if(isset($this->_order_to_render))
        {
            $columns =  Arr::sort_by_keys($this->_columns, $this->_order_to_render);
        }
        else
        {
            $columns = $this->_columns;
        }
            
        $toRes = array(
            'title' => $this->title,
            'enctype' => $this->enctype,
            'fields' => $columns,
        );
        
        foreach(array('groups','menu','fields_to_save','icon','capabilities','filter') as $col)
                if(isset($this->$col))
                    $toRes[$col] = $this->$col;
        
        
        return $toRes;
    }
    
    protected function _columns_type(){
        return array();
    }
    
    protected function _extra_columns_type(){
        return array();
    }
    
    protected function _foreign_column_type()
    {
        return array();
    }
    
    public function get_nameORM()
    {
        return $this->_nameORM;
    }
    
    protected function _fields_to_save()
    {
        return array();
    }
    
    protected function _get_capability_name()
    {
        if(isset($this->_capablity_name))
            return;
        
        // si prova a prendere la capabiliti dal nome del datastruct
        $this->_capablity_name = strtolower(preg_replace('/[_]+/', '-', trim(substr(get_class($this), 11))));
    }
    
    protected function _filter_by_role(&$data)
    {
        foreach($data as $n => $values)
        {
            if(!isset($values['roles']))
                continue;

            // nel caso ci sia la capability si controlla se l'utente la possiede
            if(!in_array($this->user->main_role_id, $values['roles']))
                    unset($data[$n]);
        }
    }


    protected function _filter_by_capability(&$data)
    {
        foreach($data as $n => $values)
        {
            if(!isset($values['capability']))
                continue;

            // nel caso ci sia la capability si controlla se l'utente la possiede
             if(!$this->user->allow_capa($values['capability']))
                 unset($data[$n]);
        }

    }


    protected function _set_capabilities()
    {
        $this->_get_capability_name();
        
        $this->capabilities = array(
            self::CAPA_LIST,
            self::CAPA_INSERT,
            self::CAPA_UPDATE,
            self::CAPA_DELETE,
        );
        
        // si può inserire o salvare solo nella lingua di default
         if($this->lang != $this->lang_default)
            unset($this->capabilities[1],$this->capabilities[3]);
        
        // si fa la query sul db per le capabilities
        if($this->user->main_role_id == 12)
            return;
        
        $capabilities = $this->user->get_allow_capabilities(FALSE,array(array('name','LIKE','%'.$this->_capablity_name.'%')));

        $from = strlen($this->_capablity_name);
        $tmp = array();
       foreach($capabilities as $capability)
        {
            $capa = substr($capability,strpos($capability,$this->_capablity_name) + strlen($this->_capablity_name) + 1);
            if(in_array($capa, $this->capabilities))
                    $tmp[] = $capa;
        }
        $this->capabilities = $tmp;
        
        
        $this->capabilities = $tmp;
    }
}
