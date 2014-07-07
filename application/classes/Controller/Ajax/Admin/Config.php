<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Config extends Controller_Ajax_Auth_Strict{
    
    public $config;

    public function before()
    {
        parent::before();

        $this->config = new stdClass();

        // si inserisce anche lo stato di sviluppo del software
        $this->config->environment = Kohana::$environment;

    }
    
    public function action_index()
    {
        $this->_set_roles();
        $this->_set_authuser();
        $this->_set_i18n();
        $this->_set_timezone();
        $this->_set_background_layer();
        $this->_set_menu();
        $this->_set_url();
        $this->_set_global_configs();
        
        
        /**
         * Popolamento oggetto config per risposta
         */
        $this->jres->data->config = $this->config;
    }
    
    protected function _set_i18n()
    {
        $this->config->i18n = I18n::lang();
    }

    
    /**
     * Si recuperano i dati dalla tabella global_configs e si inviano al config se indicato
     */
    protected function _set_global_configs()
    {
        $cds = ORM::factory('Global_Config')
                ->where('to_config','IS',DB::expr('true'))
                ->find_all();
        
        foreach($cds as $cd)
        {
            switch($cd->parametro)
            {
                case "default_extent":
                    $extentStr = preg_split("/,/", $cd->valore);
                    $valore = array(
                        'minx'=>(float)$extentStr[0],
                        'maxx'=>(float)$extentStr[1],
                        'miny'=>(float)$extentStr[2],
                         'maxy'=>(float)$extentStr[3]
                        );
                    
                    
                break;
            
                default:
                    $valore = $cd->valore;
            }
            $this->config->{$cd->parametro} = $valore;
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
        $this->config->authuser = Controller_Ajax_Admin_User::user_data_plus($this->user);
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
    
    protected function _set_background_layer()
    {
        // si recuperano solo i layers che del backend
        $bkls = ORM::factory('Background_Layer')->getLayersBySection('BACKEND');
        $this->config->background_layer = array();
        foreach($bkls as $bkl)
            $this->config->background_layer[] = $bkl->as_array();
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
            'dStruct'=> '/jx/datastruct', 
            'token' => '/jx/token',
            'filter'=> '/jx/filterdata?f=',
            'itinerary' => '/jx/admin/itinerary',
            'poi' => '/jx/admin/poi',
            'path' => '/jx/admin/path',
            'user' => 'jx/admin/user',
            'image_poi' => 'jx/admin/imagepoi',
            'image_path' => 'jx/admin/imagepath',
            'image_itinerary' => 'jx/admin/imageitinerary',
            'video_poi' => 'jx/admin/videopoi',
            'video_path' => 'jx/admin/videopath',
            
            
        );
          
    }
    
    public function _set_menu()
    {
        $menu = Kohana::$config->load('menu');
        $this->config->menu =  $menu['main'];
    }

}