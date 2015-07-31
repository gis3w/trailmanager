<?php defined('SYSPATH') or die('No direct script access.');


class Email_Passagestate_CHIUSANOTIFICATA {
    
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
        // FOR REPORT
        $mailTo = isset($this->highliting->highliting_user->email) ? array($this->highliting->highliting_user->email) : (isset($this->highliting->anonimous_data->email) ? array($this->highliting->anonimous_data->email) : NULL);
        if(isset($mailTo))
        {
            $bodyMail = View::factory('email/passagestate/chiusanotificata');
            $bodyMail->highliting = $this->highliting; 
            $this->_bodyMail =$bodyMail->render(); 
            $this->_subjects = $config['pre_email_subject'].__('Higlighting closed'); 
            $this->_mailTo = $mailTo;
        }
        
        
    }
    
    public function send()
    {
        $config = Kohana::$config->load('global');
        if(isset($this->highliting->highliting_user->id))
            Mail::factory(Mail::TYPE_HTML)
                   ->from($config['email_from'])
                   ->to($this->_mailTo)
                   ->subject($this->_subject)
                   ->body($this->_bodyMail)
                   ->send();
    }
}
