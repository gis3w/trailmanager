<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Classe principale come base per tutti i controller
 *
 * @package    Gis3W
 * @category   Controller
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2013 Gis3W
 * @license    http://kohanaframework.org/license
 */

abstract class Controller_Base_Main extends Controller_Template {

    // Parametri di base  per il controlle/template
    public $tpl = NULL;          # variabile dove inserisco il template principale
    public $template = NULL;   # oggetto template di controller_template
    public $base_template_config = "layout.main_tpl";
    public $base_css_config = "layout.css_base";
    public $base_js_config = "js.js_base";
    public $img_path;            # path alla cartella delle immagini
    public $jss = array();    # array contenente i js da file da caricare
    public $csss = array();     # array contenente i css da file da caricare
    public $jspage = "";         # script js da caricare dopo specifico per la pagina degli altri script
    public $jspre = "";          # script js da caricare prima degli altri script
    public $content = NULL;
    
    public $data_browser = array();
    public $checkBrw;
    

    /* per i menu dell'applicazione */
    public $main_menu = array();

    /* per le zone del maion template */
    public $tcontent;
    public $tlogin; // elemento dedicato alla div di login
    public $tnavbar = 'zone/navbar'; // template per la nav bar
    



    public function __construct(Request $request, Response $response){
        // assegnazione del template di base
        $this->template = $this->tpl;
        
        if(!isset($this->template))
        {
            $this->template = Kohana::$config->load($this->base_template_config);
        }
        elseif($this->template === 'NONE')
        {
            $this->template = NULL;
        }

        // si passa per riferimento per scorciare il nome della variabile template del template controlle
        $this->tpl = &$this->template;

        // richiamimao l'istanza da cui eredita passandogli l'istanza della richiesta corrente
        parent::__construct($request,$response);

    }

    public function  before() {
      // si richiama prima per istanziare la vista globale
        parent::before();
         
         // controllo del browser
         $this->data_browser = $this->request->user_agent(array('browser','version'));
         
         // si secupera solo la prima numero di vesrione del version
         $this->data_browser['version'] = substr($this->data_browser['version'], 0, strpos($this->data_browser['version'],"."));
         
         // ci facciamo il controllo del browser
       $this->checkBrw = TRUE;
       
       // se mobile
//       if($this->detect->isMobile() AND Route::name($this->request->route()) !== 'mobile')
//           HTTP::redirect ('mobile');

         // istanziamento delle varie view di base
        // si recurera il dato dal config layut

        // caso particolare per lil login
        if(isset($this->tlogin))
        {
            $this->tlogin = View::factory($this->tlogin);
            $this->tpl->tlogin = $this->tlogin;
        }
        
        if(isset($this->tnavbar))
        {
            $this->tnavbar = View::factory($this->tnavbar);
            $this->tpl->tnavbar = $this->tnavbar;
        }
        // si settano nelle viste i menu
        View::bind_global('main_menu', $this->main_menu);
        View::bind_global('aside_menu', $this->aside_menu);

        // si settano i parametri globali per le views
        $this->img_path = Kohana::$config->load('layout.img_path');
        View::set_global('img_path',$this->img_path);
        View::set_global('css_path',Kohana::$config->load('layout.css_path'));
        View::set_global('js_path',Kohana::$config->load('js.js_path'));
        View::set_global('logo_main',Kohana::$config->load('layout.logo_main'));
        View::set_global('logo_navbar',Kohana::$config->load('layout.logo_navbar'));
        View::set_global('logo_print',Kohana::$config->load('layout.logo_print'));
        View::set_global('logo_email',Kohana::$config->load('layout.logo_email'));
        
        View::set_global('ambient',  Kohana::$config->load('global.ambient'));
        
      
        if(isset($this->tpl))
        {
            $global_data = Kohana::$config->load('global');
                        
            $this->tpl->title = isset($global_data['html_tag']['title']) ? $global_data['html_tag']['title'] : Kohana::$config->load('layout.title_default');

            //settaggio title
            if(isset($this->title)){
                $this->tpl->title .= ' :: '.$this->title;
            }

            if(isset($this->tcontent))
                $this->tpl->tcontent = View::factory ($this->tcontent); 

            // Settiamo i file Js da caricare
            $this->tpl->jss = Arr::push(Kohana::$config->load($this->base_js_config),$this->jss);

            //jscompile
            if(Kohana::$config->load('js.js_compile')){
                $js_cache_arr = array();
                $js_no_cache_arr = array();
                $js_to_exlude = Kohana::$config->load('js.js_exlude_to_compile');
                foreach($this->tpl->jss as $js){
                    if(in_array($js,$js_to_exlude))
                    {
                        $js_no_cache_arr[] = $js;
                    }
                    else
                    {
                        $js_cache_arr[] = APPPATH."../".Kohana::$config->load('js.js_path').$js;
                    }
                    
                }
                $this->tpl->jss = $js_no_cache_arr;
                $this->tpl->js_cache = CoffeeScript::compile($js_cache_arr);
            }
            
             
            // per eventuali moduli che devonoi aggiungere durante eventi... forse :)
            //Event::run('tg.jss', $this->tpl->jss);

            $this->tpl->jspre = $this->jspre;
            $this->tpl->jspage = $this->jspage;


            // Settiamo i file Css da caricare
            $this->tpl->csss = Arr::push(Kohana::$config->load($this->base_css_config),$this->csss);
            
            
            if(Kohana::$config->load('layout.css_compile')){
                $css_cache_arr = array();
                foreach($this->tpl->csss as $css_file => $css_type){
                    $css_cache_arr[] = APPPATH."../".Kohana::$config->load('layout.css_path').$css_file;
                }
                $this->tpl->css_cache = CoffeeScript::compile($css_cache_arr,  CoffeeScript::FORMAT_TYPE_CSS);
            }
            
        }

    }
    
      protected function _get_main_menu()
     {
         $items_conf = Kohana::$config->load('menu.main'); 
         return $this->_build_menu($items_conf);
         
     }
     
     protected function _build_menu($menu)
     { 
         $items = array();
         foreach($menu as $nid => $par)
         {
             
             if(!is_null($par['capability']) AND !$this->user->role->allow_capa($par['capability']))
                 continue;
             
             $arr = array();
             $arr['id'] = $par['id'];
             $arr['name'] = $par['name'];
             $arr['url'] = '#';
             $arr['icon'] = $par['icon'];
             
             if(isset($par['tabs']))
                 $arr['tabs'] = $this->_build_menu ($par['tabs']);
             
             $items[] = $arr;
             
         }
         
         return $items;
     }
    



} 
