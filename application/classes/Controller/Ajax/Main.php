<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe astratta generica per le risposte ajax del sistema
 *
 * @package    Gis3W
 * @category   Controller
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2013 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

abstract class Controller_Ajax_Main extends Kohana_Controller_Api_Main{
    
    public function before() {
        
        // si controlla che la richiesta sia di tipo ajax altrimenti... redirect a 404
        if(!$this->request->is_ajax() AND !Kohana::$environment === Kohana::DEVELOPMENT)
        {
            // PER EVITARE ERRORI LOOP
            if($this->request->directory() == 'ajax' AND $this->request->controller() !== 'error')
            {
                // una eccezione che vinen richimato dalla richieste non ajax
                throw HTTP_Exception::factory(403,'Accesso negato, solo tramite richiesta ajax');
            }
        }
        
        //si carica al livello generale anche la struttura
        $this->datagram = Tree::factory(Kohana::$config->load('datagram.items'));

        
        parent::before();
    }
}