<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Controlle di gestione delle risposte di errore delle richieste ajax
 * 
 * @package    Gis3W
 * @category   Controller
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2012 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html 
 */

abstract class Kohana_Controller_Api_Error extends Kohana_Controller_REST_Error
{
        
        public function after() {
         // lettura del protobus
         Path::getProtoClass("statusmessage");
          
            $res = new statusmessage\Status();
            $error = new statusmessage\Status\Error();
            $error->errcode = (int)$this->request->action();
            $error->errmsg = $this->_message;
            $error->errfile = $this->_file;
            $error->errline = $this->_line;

            $res->status = 0;

            $res->setError($error);
            
             $this->response->body($res->serialize());
        }
}
