<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Config extends Controller_Ajax_Main{
    
    public $config;
    
    protected $_icon_uri ="/download/typologyicon/index/";
    protected $_marker_uri ="/download/typologymarker/index/";

    public function before()
    {
        parent::before();

        $this->config = new stdClass();

        // si inserisce anche lo stato di sviluppo del software
        $this->config->environment = Kohana::$environment;

    }
    
    public function action_index()
    {
//        $this->_set_roles();
//        $this->_set_authuser();
        $this->_set_i18n();
        $this->_set_typologies();
//        $this->_set_timezone();
//        $this->_set_menu();
        $this->_set_url();
        $this->_set_background_layer();
        
        
        /**
         * Popolamento oggetto config per risposta
         */
        $this->jres->data->config = $this->config;
    }
    
    protected function _set_background_layer()
    {
        $this->_get_table('Background_Layer');
    }
    
    protected function _set_i18n()
    {
        $this->config->i18n = I18n::lang();
    }
    
    protected function _set_typologies()
    {
        $this->_get_table('Typology');
        foreach($this->config->typology as &$typologies)
        {
            $typologies['icon'] =$this->_icon_uri.$typologies['icon'];
            $typologies['marker'] =$this->_marker_uri.$typologies['marker'];;
        }
        
    }


    protected function _set_roles()
    {
        $orm = ORM::factory('Role')->where('name','!=','login');
        if($this->user->main_role_id !== 12)
        {
            $orm->where('id','!=',12)
                    ->where ('id','!=',$this->user->main_role_id);
        }

            
        $this->_get_table($orm);
    }
    

    protected function _set_authuser()
    {
        $this->config->authuser = Controller_Ajax_User::user_data_plus($this->user);
    }


    
    protected function _get_table($ORM_name)
    {
        
        if($ORM_name InstanceOf Model)
        {
            $results =  $ORM_name;
            $name = $results->object_name();
        }
        else
        {
            $results = ORM::factory($ORM_name);
            $name = strtolower($ORM_name);
        }
        $results = $results->find_all()->as_array();
        $toRes = array();
        foreach($results as $res)
            $toRes[] = $res->as_array();
        
        $this->config->$name  = $toRes;
        
        
    }

    
    protected function _set_timezone()
    {
        $this->config->timezone = date_default_timezone_get();
    }
    
    protected function _set_url()
    {
        $this->config->urls =  array(
            'logout'=> '/login/out',
            'i18n'=>  '/jx/i18n/', //language
            'config'=> '/jx/config', // i18n
            'dStruct'=> '/jx/datastruct', // /jx/datastruct?tb=user          
            'filter'=> '/jx/filterdata?f=',
            
        );
          
    }
    
    public function _set_menu()
    {
        $menu = Kohana::$config->load('menu');
        $this->config->menu =  $menu['main'];
    }

}