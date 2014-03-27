<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe astratta per i controlle strettamente sotto autenticazione
 *
 * @package    Gis3W
 * @category   Controller
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2013 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

abstract class Controller_Ajax_Auth_Strict extends Controller_Ajax_Main {
    
    /**
     * La sessione dell'applicazione
     * @var Instance of Session class
     */
    public $session;
    
    public $user;
    
    protected $_current_year;
    
    protected $_url_filters;


    public function before()
    {

           parent::before();

            // si imposta l'anno corrente
            $this->_current_year = (int)date('Y');

           // parte con la autenticazione
           // si avvia la sessione per registrare la chiamata
           $this->session= Session::instance();
           // ORA parte il processo di autenticazione
           $this->a = Auth::instance();
           

            if (!$this->a->logged_in()){

                throw new HTTP_Exception_403 (SAFE::message('ehttp','403_auth_strict'));

            }
            


            // si recuperano i dati di autenticazione
            $this->user = $this->a->get_user();
            
            

            /*
         * Controllo ACL sono per le richieste iniziali non interne
          *
          * Dopo il before del controller rest così prende gli action rest
          * che è impostato specificatemente in ogni controller
         */
            $this->_ACL();
            


	}

        /**
         * Metodo generico per il controllo dell'ACL sul controller
         */
        protected function _controller_ACL()
        {
            // recuper del controller
            $controller = strtolower($this->request->controller());
            $directory = strtolower(preg_replace("/Ajax/", "", $this->request->directory()));
            $directory = preg_replace("/\//", "",$directory);
            
            $controller = $directory  ? $directory."-".$controller: $controller;
            
            if(Kohana::$environment === Kohana::DEVELOPMENT)
                Kohana::$log->add(LOG::DEBUG, "ACL CONTROLLER FIT: ".$controller    );
            
            if($this->request->action() === 'create' AND !$this->user->allow_capa($controller.'-insert'))
                    throw HTTP_Exception::factory(403,SAFE::message('capability','default',NULL,$controller.'-insert'));
            
            if($this->request->action() === 'update' AND !$this->user->allow_capa($controller.'-update'))
                    throw HTTP_Exception::factory(403,SAFE::message('capability','default',NULL,$controller.'-update'));

            if($this->request->action() === 'index' AND is_numeric($this->id) AND !$this->user->allow_capa($controller.'-get'))
                    throw HTTP_Exception::factory(403,SAFE::message('capability','default',NULL,$controller.'-get'));
            
            if($this->request->action() === 'delete' AND is_numeric($this->id) AND !$this->user->allow_capa($controller.'-delete'))
                    throw HTTP_Exception::factory(403,SAFE::message('capability','default',NULL,$controller.'-delete'));

             if($this->id === 'list' AND !$this->user->allow_capa($controller.'-list'))
                    throw HTTP_Exception::factory(403,SAFE::message('capability','default',NULL,$controller.'-list'));
        }
        
        /**
         * Metodo generico per il controllo dell'ACL sulla directory
         */
        protected function _directory_ACL()
        {
            $directory = strtolower(preg_replace("/Ajax\//", "", $this->request->directory()));
            
             if(Kohana::$environment === Kohana::DEVELOPMENT)
                Kohana::$log->add(LOG::DEBUG, "ACL DIRECOTORY FIT: ".$directory);
            
             if(!$this->user->role->allow_capa('access-'.$directory) AND !$this->user->allow_capa($directory.'-usage'))
                    throw HTTP_Exception::factory(403,SAFE::message('capability','default',NULL,'access-'.$directory));
        }
        
        protected function _get_url_filters()
        {
            if(!isset($this->_url_filters))
                   $this->_url_filters = array();

            if(!isset($_GET['filter']))
                return TRUE;

            $filters = preg_split('/,/', $_GET['filter']) ;

            foreach($filters as $filter)
           {
               if(!empty($filter))
               {
                   list($key,$value) = preg_split('/:/',$filter);
                   $this->_url_filters[$key] = $value; 
               }
           }
        }
   
} 
