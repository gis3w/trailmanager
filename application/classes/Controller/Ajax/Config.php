<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Config extends Controller_Ajax_Auth_Nostrict{
    
    public $config;
    
    protected $_icon_uri ="download/typologyicon/index/";
    protected $_icon_pathmode_uri ="download/pathmodeicon/index/";
    protected $_marker_uri ="download/typologymarker/index/";

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
        $this->_set_authuser();
        $this->_set_i18n();
        $this->_set_typologies();
        $this->_set_highliting_typologies();
        $this->_set_survey_codes_poi();
//        $this->_set_timezone();
//        $this->_set_menu();
        $this->_set_url();
        $this->_set_page_urls();
        $this->_set_background_layer();
        $this->_set_global_configs();
        
        
        /**
         * Popolamento oggetto config per risposta
         */
        $this->jres->data->config = $this->config;
    }

    public function _set_survey_codes_poi()
    {
        foreach (array(
                     'Pt_Inter_Poi',
                     'Strut_Ric_Poi',
                     'Aree_Attr_Poi',
                     'Insediam_Poi',
                     'Pt_Acqua_Poi',
                     'Tipo_Segna_Poi',
                     'Stato_Segn_Poi',
                     'Fatt_Degr_Poi',
                     'Pt_Socc_Poi',
                     'Coin_In_Fi_Poi',
                     'Prio_Int_Poi',
                     'Nuov_Segna_Poi',

                 ) as $tb)
        {
            $pms = ORM::factory($tb)->find_all();
            $tb_dcase = strtolower($tb);
            $this->config->{$tb_dcase} = array();
            foreach($pms as $pm)
                $this->config->{$tb_dcase}[] = $pm->as_array();
        }


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


    protected function _set_background_layer()
    {
                // si recuperano solo i layers che del backend
        $bkls = ORM::factory('Background_Layer')->getLayersBySection('FRONTEND');
        $this->config->background_layer = array();
         foreach($bkls as $bkl)
        {
            $arr = $bkl->as_array();
            unset($arr['layer_type_id']);
            $arr['layer_type'] = $bkl->layer_type->type;
            $this->config->background_layer[] = $arr;
        }
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
            $typologies['icon'] =(isset($typologies['icon']) AND $typologies['icon'] != '') ? $this->_icon_uri.$typologies['icon'] : NULL;
            $typologies['marker'] =(isset($typologies['marker']) AND $typologies['marker'] != '') ? $this->_marker_uri.$typologies['marker'] : NULL;
        }
        
    }

    protected function _set_highliting_typologies()
    {
        $this->_get_table('Highliting_Typology');
        foreach($this->config->highliting_typology as &$typologies)
        {
            $typologies['icon'] =(isset($typologies['icon']) AND $typologies['icon'] != '') ? $this->_icon_uri.$typologies['icon'] : NULL;
            $typologies['marker'] =(isset($typologies['marker']) AND $typologies['marker'] != '') ? $this->_marker_uri.$typologies['marker'] : NULL;
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
        if($this->user)
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


    
    protected function _set_timezone()
    {
        $this->config->timezone = date_default_timezone_get();
    }
    
    protected function _set_url()
    {
        $this->config->urls =  array(
            'login' => 'jx/login',
            'logout'=> 'jx/logout',
            'reset_password' => 'jx/resetpassword',
            'i18n'=>  '/jx/i18n/', //language
            'token' => 'jx/token',
            'config'=> '/jx/config', // i18n
            'dStruct'=> '/jx/datastruct', // /jx/datastruct?tb=user          
            'filter'=> '/jx/filterdata?f=',
            'front_registration' => 'jx/registration',
            'front_highlitingpoi' => 'jx/highlitingpoi',
            'heightsprofilepath' => 'jx/heightsprofilepath/'
            
        );
          
    }
    
    /**
     * Set pulrs to call for pages
     */
    protected function _set_page_urls()
    {
        $pages = ORM::factory('Page')->find_all();
        $this->config->page_urls = array();
        foreach($pages as $page)
            $this->config->page_urls[$page->alpha_id] = '/jx/page/'.$page->id;
          
    }
    
    public function _set_menu()
    {
        $menu = Kohana::$config->load('menu');
        $this->config->menu =  $menu['main'];
    }

}