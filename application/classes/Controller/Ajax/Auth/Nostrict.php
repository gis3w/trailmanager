<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe astratta per i controller non  strettamente sotto autenticazione
 *
 * @package    Gis3W
 * @category   Controller
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2013 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

abstract class Controller_Ajax_Auth_Nostrict extends Controller_Ajax_Main {
    
    /**
     * La sessione dell'applicazione
     * @var Instance of Session class
     */
    public $session;
    
    public $user;
    
  
    public function before()
    {
        parent::before();
        // si avvia la sessione per registrare la chiamata
        $this->session= Session::instance();
        // ORA parte il processo di autenticazione
        $this->a = Auth::instance();

         // si recuperano i dati di autenticazione
        $this->user = $this->a->get_user();

    }
   
} 
