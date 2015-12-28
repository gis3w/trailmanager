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

abstract class Kohana_Controller_REST_Error extends Kohana_Controller_Api_Main
{
	/**
	 * @var string
	 */
	protected $_requested_page;

	/**
	 * @var string
	 */
	protected $_message;

        /**
         *
         * @var string
         */
        protected $_file;

        /**
         *
         * @var Integer
         */
        protected $_line;

	/**
	 * Pre determine error display logic
	 */
	public function before()
	{
        // per annulare ik before del controller REST
        // si DEVE METTERE CURRENT PERNCHE' È UN CHIAMATA INTERNA--- !!TESTONE!!
        $ini_action = Request::current()->action();

		parent::before();

        // si normalizza il gioco action
        $this->request->action($ini_action);
//       var_dump(Request::current());
//       exit();
        // Sub requests only!
//		if (Request::initial() !== Request::current())
//		{
			if ($message = rawurldecode($this->request->param('message')))
			{
				$this->_message = $message;
			}

			if ($requested_page = rawurldecode($this->request->param('origuri')))
			{
				$this->_requested_page = $requested_page;
			}

                        if ($file = rawurldecode($this->request->param('file')))
			{
				$this->_file = $file;
			}

                        if ($line = rawurldecode($this->request->param('line')))
			{
				$this->_line = $line;
			}
//		}
//		else
//		{
//                        //TODO:mettere nel caso di una non subridrect un Http_Exception_404 di pagina normale non ajax
//                        throw new HTTP_Exception_404('Pagina non trovata');
//
//                        // Set the requested page accordingly
//			//$this->_requested_page = Arr::get($_SERVER, 'REQUEST_URI');
//		}

		//$this->response->status((int) $this->request->action());

        $this->jres->status = 0;

        $this->jres->error->errcode = $this->request->action();

        $this->_message = rawurldecode($this->_message);
	}


	/**
	 * Serves HTTP 404 error page
	 */
	public function action_404()
	{
		
            if(Kohana::$environment === KOHANA::DEVELOPMENT AND isset($this->_message))
            {
                $this->jres->error->errmsg = $this->_message;

            }
            else
            {
                $this->jres->error->errmsg = 'Pagina richiesta non trovata!';
            }

	}

	/**
	 * Serves HTTP 500 error page
	 */
	public function action_500()
	{

            if(Kohana::$environment === KOHANA::DEVELOPMENT AND isset($this->_message))
            {
                $this->jres->error->errmsg = $this->_message;

                $this->jres->error->errline = $this->_line;

                $this->jres->error->errfile = $this->_file;


            }
            else
            {
                $this->jres->error->errmsg = ' Si è verificato un errore. Se l\'errore persiste contattare l\'amministratore del sistema!';
            }

	}

        /**
         * Metodo per gestione errori 401 non autorizzato
         */
        public function action_401()
	{

            if(Kohana::$environment === KOHANA::DEVELOPMENT AND isset($this->_message))
            {
                $this->jres->error->errmsg = $this->_message;
            }
            else
            {
                $this->jres->error->errmsg = ' Non Autorizzato';
            }

	}
        

        /**
	 * Serves HTTP 403 error page
	 */
	public function action_403()
	{

            if(Kohana::$environment === KOHANA::DEVELOPMENT AND isset($this->_message))
            {
                $this->jres->error->errmsg = $this->_message;
            }
            else
            {
                $this->jres->error->errmsg = ' Accesso negato';
            }

	}
        
         public function action_400()
        {

            if(Kohana::$environment === KOHANA::DEVELOPMENT AND isset($this->_message))
            {
                $this->jres->error->errmsg = $this->_message;
            }
            else
            {
                $this->jres->error->errmsg = ' No Basic Authentication';
            }

        }
        
}
