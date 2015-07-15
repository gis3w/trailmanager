<?php defined('SYSPATH') or die('No direct script access.');


class Email_Newhighliting {
    
    public $highliting;
    
    protected $_bodyMails = array();
    protected $_subjects = array();
    protected $_mailTos = array();


    public function __construct($highliting) {
        $this->highliting = $highliting;
        
        $this->buildBodyMail();
        
    }
    
    public function buildBodyMail()
    {
        $config = Kohana::$config->load('global');
        if(isset($this->highliting->highliting_user->id))
        {
            $bodyMail = View::factory('email/newhighliting/detector');
            $bodyMail->highliting = $this->highliting; 
            $this->_bodyMails['detector'] =$bodyMail->render(); 
            $this->_subjects['detector'] = $config['pre_email_subject'].__('Highlighting insert'); //Segnalazione correttamente rigistrata sul portale
            $this->_mailTos['detector'] = array($this->highliting->highliting_user->email);
        }
        
                
        $bodyMail = View::factory('email/newhighliting/register');
        $bodyMail->highliting = $this->highliting; 
        $this->_bodyMails['register'] =$bodyMail->render(); 
        
        $this->_subjects['register'] = $config['pre_email_subject'].__('New highlighting'); //Nuova segnalazione
        // get the protovcl user
        $protocol_user = array_keys(ORM::factory('Role')
                ->where('id','=',ROLE_PROTOCOL)
                ->find()
                ->users
                ->find_all()->as_array('email'));
        $this->_mailTos['register'] = $protocol_user;
    }
    
    public function send()
    {
        $config = Kohana::$config->load('global');
        foreach($this->_bodyMails as $who => $bodyMail)
            Mail::factory(Mail::TYPE_HTML)
                   ->from($config['email_from'])
                   ->to($this->_mailTos[$who])
                   //->to(array('lorenzetti@gis3w.it'))
                   ->subject($this->_subjects[$who])
                   ->body($bodyMail)
                   ->send();
    }

}
