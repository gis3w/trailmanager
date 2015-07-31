<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe per  l'invio generico delle mail: wrapper a swift mailer
 *
 * @package    Gis3W
 * @category   Mail
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2013 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class Kohana_Mail{

    protected static $_instance;

    protected $_transport;
    protected $_mailer;
    protected $_logger;
    protected $_message;
    protected $_type;
    protected $_main_body_template = 'email/main';

    const TYPE_HTML = 'text/html';
    const TYPE_PLAIN  = 'text/plain';
    
        protected $_actions = array(
        'subject','to','from','body'
    );

    /**
     * Main construct
     */
    private function  __construct($type = Mail::TYPE_PLAIN) {
        
       if(!$this->_transport instanceof Swift_MailTransport AND !$this->_transport instanceof Swift_SmtpTransport)
       {
           // recupero dati di configurazione verificando che il soggetto che invia la mail non sia un ente
           $u = Auth::instance()->get_user();

           //$eMailConfig = $u->main_role === 'ente' ? $u->ente->as_array(): Kohana::config('mail');
           $eMailConfig = Kohana::$config->load('mail');

           if($eMailConfig['pec'])
           {
               $this->_transport = Swift_SmtpTransport::newInstance ($eMailConfig['smtp'],$eMailConfig['port'],$eMailConfig['method'])
                       ->setUsername($eMailConfig['username'])
                       ->setPassword($eMailConfig['password']);
           }
           else
           {
               $this->_transport = Swift_MailTransport::newInstance ();
           }

       }

       if(!$this->_mailer instanceof Swift_Mailer) $this->_mailer = Swift_Mailer::newInstance ($this->_transport);
       
       $this->_logger = new Swift_Plugins_Loggers_ArrayLogger();
       $this->_mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($this->_logger));

       if(!$this->_message instanceof Swift_Message) $this->_message = Swift_Message::newInstance ();
       
       $this->_type = $type;

    }

    /**
     * Singletoon for Swift_Mailar Class
     * @return Instance Kohana_Mail
     */
    public static function factory($type = Mail::TYPE_PLAIN){

      if(self::$_instance === NULL){

          self::$_instance = new Mail($type);
          
      }

      return self::$_instance;

    }

    public function  __set($name, $value) {

        if(in_array($name,$this->_actions)){

            $method = "set".ucfirst($name);

            if($name === 'body')
            {
                if($this->_type === Mail::TYPE_HTML)
                    $value = $this->_html_body($value);
                
                if(Kohana::$environment === Kohana::DEVELOPMENT)
                    file_put_contents ('/tmp/email.html', $value);
                
                $this->_message->$method($value,$this->_type);
            }
            else
            {
                $this->_message->$method($value);
            }
            

        }else{

            throw new Kohana_Exception('Spiacente ma la "field" non esiste in Mail Class', array('field' => $name ), 500);
            
        }

    }
    
    
    public function __call($name, $arguments) {
        if(in_array($name,$this->_actions)){
            $this->$name = $arguments[0];
            return $this;
        }
        else
        {
            trigger_error('Call to undefined method '.  get_class($this).'::'.$name.'()', E_USER_ERROR);
        }
    }
    
       protected function _html_body($content)
    {
        // costruzione html del body mediante template
        $html_body_template = View::factory($this->_main_body_template);
        $layout = Kohana::$config->load('layout');
        $html_body_template->layout = $layout;
        $global_data = Kohana::$config->load('global');
        $html_body_template->global_data = $global_data;
        $html_body_template->body_content = $content;
        return $html_body_template->render();

        
    }


    public function embed($image){
        return $this->_message->embed(Swift_Image::fromPath($image));

    }

    public function send(){
        $this->_mailer->send($this->_message);
        error_log($this->_logger->dump());
    }

}
