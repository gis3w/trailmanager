<?php defined('SYSPATH') or die('No direct script access.');

/**
 *  Controller che mi da la pagina di login
 *
 * @package    Gis3W
 * @category   Controller
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2012 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

class Controller_Login extends Controller_Base_Main {

    public $tnavbar = NULL;
    public $tlogin = 'login';
    
    public function before() {
        parent::before();
        
        // si aggiunge il form di login
        $this->tlogin->form = View::factory('global/login/form');
    }

    /**
    * Azione iniziale e principale di atutenticazione
    */
    public function action_index()
    {

        $this->session = Session::instance();

        // se si prova ad accedere alogin una volta autenticati si fa il redirect sulla home
        if(Auth::instance()->logged_in()){
            if($this->session->get("requested_url")) HTTP::redirect($this->session->get("requested_url"));
            HTTP::redirect('/');
        }

       // azzeramento dei parametri per il form.... dentro la view
       $this->tlogin->form->username = '';
       $this->tlogin->form->password = '';

        if ($_POST) {  
            if(Auth::instance()->login($_POST['username'],$_POST['password'],false)){

                // Controllo della avvenuta conferma di registrazione
                $u = Auth::instance()->get_user();

                    if($this->session->get("requested_url")) HTTP::redirect($this->session->get("requested_url"));
                    HTTP::redirect('home');

            } else {
                
                $this->tlogin->form->username = $_POST['username']; //Redisplay username (but not password) when form is redisplayed.
                $this->tlogin->message = SAFE::message('auth', 'login_err');

                // si logga il tentativo sbagliato di login (da mantenere in fase di testing poi per privacy direi di scluderla)
                Kohana::$log->add(Log::NOTICE, 'Tentativo di login dall\'HOST: '.$_SERVER['REMOTE_ADDR'].'('.gethostbyaddr($_SERVER['REMOTE_ADDR']).') con  USERNAME: '.$_POST['username'].' e PASSWORD: '.$_POST['password']);

            }
        }
    }

    /**
     * Classe le il logout e i redirect alla pagina di login
     */
    public function action_out(){

        Auth::instance()->logout(TRUE);
        HTTP::redirect('login');

    }
    
    


} // End Prova
