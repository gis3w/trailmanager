<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe astratta per i controller nonstrettamente sotto autenticazione
 *
 * @package    Gis3W
 * @category   Controller
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2013 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

abstract class  Controller_Export_Base_Auth_Nostrict extends Controller_Export_Main {

    public function before()
    {
        Controller_Base_Main::before();

        $this->session= Session::instance();

        // ORA parte il processo di autenticazione
        $this->a = Auth::instance();

        // si recuperano i dati di autenticazione
        $this->user = $this->a->get_user();
        View::set_global('user', $this->user);


    }


} 
