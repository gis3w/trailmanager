<?php defined('SYSPATH') or die('No direct script access.');


class Email_Passagestate_ASSEGNATASUPERVISORINESECUZIONE {
    
    public $highliting;
    
    protected $_bodyMail;
    protected $_subject;
    protected $_mailTo;


    public function __construct($highliting) {
        $this->highliting = $highliting;
        
        $this->buildBodyMail();
        
    }
    
    public function buildBodyMail()
    {
        $config = Kohana::$config->load('global');
        $bodyMail = View::factory('email/passagestate/assegnatasupervisorinesecuzione');
        $bodyMail->highliting = $this->highliting; 
        $this->_bodyMail =$bodyMail; 
        $this->_subject = $config['pre_email_subject'].__('A new Highlighting is assigned to you'); 
        $this->_mailTo = array($this->highliting->executor_user->email);
        
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
