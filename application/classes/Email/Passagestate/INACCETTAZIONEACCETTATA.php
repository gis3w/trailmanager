<?php defined('SYSPATH') or die('No direct script access.');


class Email_Passagestate_INACCETTAZIONEACCETTATA {
    
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
        $bodyMail = View::factory('email/passagestate/inaccettazioneaccettata');
        $bodyMail->highliting = $this->highliting; 
        $this->_bodyMail =$bodyMail; 
        $this->_subject = $config['pre_email_subject'].__('Highlighting approved'); //Segnalazione correttamente rigistrata sul portale
        $this->_mailTo = isset($this->highliting->highliting_user->email) ? array($this->highliting->highliting_user->email) : (isset($this->highliting->anonimous_data->email) ? array($this->highliting->anonimous_data->email) : NULL);
    }
    
    public function send()
    {
        $config = Kohana::$config->load('global');
        if(isset($this->_mailTo))
            Mail::factory(Mail::TYPE_HTML)
                   ->from($config['email_from'])
                   ->to($this->_mailTo)
                   ->subject($this->_subject)
                   ->body($this->_bodyMail)
                   ->send();
    }
}
