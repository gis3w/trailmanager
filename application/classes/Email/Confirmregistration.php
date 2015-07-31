<?php defined('SYSPATH') or die('No direct script access.');


class Email_Confirmregistration {
    
    public $user;
    
    protected $_bodyMail;
    protected $_subject;
    protected $_mailTo;


    public function __construct($user) {
        $this->user = $user;
        
        $this->buildBodyMail();
        
    }
    
    public function buildBodyMail()
    {
        $config = Kohana::$config->load('global');
        $bodyMail = View::factory('email/confirmregistration');
        $bodyMail->global_data = $config;
        $bodyMail->user = $this->user; 
        $this->_bodyMail =$bodyMail; 
        $this->_subject = $config['pre_email_subject'].__('Confirm registration'); //Segnalazione correttamente rigistrata sul portale
        $this->_mailTo = array($this->user->email);
        
    }
    
    public function send()
    {
        $config = Kohana::$config->load('global');
        Mail::factory(Mail::TYPE_HTML)
               ->from($config['email_from'])
               ->to($this->_mailTo)
               ->subject($this->_subject)
               ->body($this->_bodyMail)
               ->send();
    }
}
