<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Config extends Controller_Ajax_Auth_Strict{
    
    public $config;

    protected $_highliting_typology_icon_uri ="download/highlitingtypologyicon/index/";

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
        $this->_set_highliting_typologies();
        $this->_set_menu();
        $this->_set_url();
        $this->_set_survey_codes_path_segments();
        $this->_set_survey_codes_poi();
        $this->_set_global_configs();
        $this->_set_states();
        $this->_set_crud_menu();
        $this->_set_icons_data();

        
        
        /**
         * Popolamento oggetto config per risposta
         */
        $this->jres->data->config = $this->config;
    }

    protected function _set_crud_menu()
    {
        $this->config->crud_menu = array(
            'position' => 'TR',
            'orentation' => 'H'
        );
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

    protected function _set_highliting_typologies()
    {
        $this->_get_table('Highliting_Typology');
        foreach($this->config->highliting_typology as &$typologies)
        {
            $typologies['icon'] =(isset($typologies['icon']) AND $typologies['icon'] != '') ? $this->_highliting_typology_icon_uri.$typologies['icon'] : NULL;
        }

    }
    
    protected function _set_background_layer()
    {
        // si recuperano solo i layers che del backend
        $bkls = ORM::factory('Background_Layer')->getLayersBySection('BACKEND');
        $this->config->background_layer = array();
        foreach($bkls as $bkl)
        {
            $arr = $bkl->as_array();
            unset($arr['layer_type_id']);
            $arr['layer_type'] = $bkl->layer_type->type;
            $this->config->background_layer[] = $arr;
        }
            
    }

    protected function _set_states()
    {
        $states = ORM::factory('Highliting_State')->find_all();
        $this->config->states = array();
        foreach($states as $state)
            $this->config->states[] = $state->as_array();
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
            'path_segment' =>'/jx/admin/pathsegment',
            'area' => '/jx/admin/area',
            'user' => 'jx/admin/user',
            'anonimous_highlitings_data' => 'jx/admin/anonimoushighlitingsdata',
            'image_poi' => 'jx/admin/imagepoi',
            'image_path' => 'jx/admin/imagepath',
            'image_area' => 'jx/admin/imagearea',
            'image_itinerary' => 'jx/admin/imageitinerary',
            'video_poi' => 'jx/admin/videopoi',
            'video_path' => 'jx/admin/videopath',
            'video_area' => 'jx/admin/videoarea',
            'highliting_poi' => '/jx/admin/highlitingpoi',
            'image_highliting_poi' => 'jx/admin/imagehighlitingpoi',
            'highliting_path' => '/jx/admin/highlitingpath',
            'image_highliting_path' => 'jx/admin/imagehighlitingpath',
            'highliting_summary' => 'jx/admin/highlitingsummary',
            'theme' => 'jx/admin/settheme'
            
            
        );
          
    }
    
    public function _set_menu()
    {
        $menu = Kohana::$config->load('menu');
        $this->config->menu =  $menu['main'];
    }



    public function _set_survey_codes_path_segments()
    {
        foreach (array(
                     'Tp_Trat_Segment',
                     'Class_Ril_Segment',
                     'Tp_Fondo_Segment',
                     'Diff_Segment',
                     'Percorr_Segment',
                     'Rid_Perc_Segment',
                     'Morf_Segment',
                     'Ambiente_Segment',
                     'Cop_Tel_Segment',
                     'Utenza_Segment'
                 ) as $tb)
        {
            $pms = ORM::factory($tb)->find_all();
            $tb_dcase = strtolower($tb);
            $this->config->{$tb_dcase} = array();
            foreach($pms as $pm)
                $this->config->{$tb_dcase}[] = $pm->as_array();
        }


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

    protected function _set_icons_data()
    {
        $this->config->icon_data = array(
            'width' => SVG2PNGPinmap::$dim_pin[0],
            'height' => SVG2PNGPinmap::$dim_pin[1],
        );
    }


}