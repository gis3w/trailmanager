<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe astratta per i controller strettamente sotto autenticazione
 *
 * @package    Gis3W
 * @category   Controller
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2013 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

abstract class  Controller_Print_Base_Auth_Strict extends Controller_Print_Base {


    public function before() {
        parent::before();

        // parte con la autenticazione
        // si avvia la sessione per registrare la chiamata

        $this->session= Session::instance();


        // ORA parte il processo di autenticazione
        $this->a = Auth::instance();

        if (!$this->a->logged_in())
        {
            $this->session->set("requested_url","/".$this->request->uri());
            HTTP::redirect('login');
        }

        // si recuperano i dati di autenticazione
        $this->user = $this->a->get_user();
        View::set_global('user', $this->user);

        if(
            $this->request->controller() !== 'Firstlogin' AND
            !isset($this->user->data_first_change_password) AND
            $this->user->main_role_id !== 12
        )
            HTTP::redirect('firstlogin');

        // si costruisce il menu
        $this->main_menu = $this->_get_main_menu();

        /*
     * Controllo ACL sono per le richieste iniziali non interne
      *
      * Dopo il before del controller rest così prende gli action rest
      * che è impostato specificatemente in ogni controller
     */
        $this->_ACL();

        $this->_initialize();

    }

    protected function _ACL()
    {
        if(!$this->user->role->allow_capa('print-pdf'))
            throw HTTP_Exception::factory(403,SAFE::message('capability','default',NULL,'print-pdf'));

        // recuper del controller
        $controller = preg_replace("/_/", "-",strtolower($this->request->controller())); ;
        $directory = preg_replace("/\//", "-",strtolower($this->request->directory()));

        $controller = $directory."-".$controller;

        if(!$this->user->role->allow_capa($controller))
            throw HTTP_Exception::factory(403,SAFE::message('capability','default',NULL,$controller));

    }


} 
