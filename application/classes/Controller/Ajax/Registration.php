<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Registration extends Controller_Ajax_Admin_User{
    
    protected $_exeLogin = FALSE;

    protected $_pagination = FALSE;
    protected $_datastruct = "Front_Registration";
    protected $_email_send = TRUE;


    public function action_delete() {
       throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));  
    }
    
    public function action_update() {
         throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }
    
    public function action_index() {
         throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }
    
    
    protected function _validation_user()
    {
        
        $this->_vorm = Validation::factory($_POST);
        
        // si aggiungono le rules per user
        foreach($this->_orm->rules() as $field => $rules)
            $this->_vorm->rules($field,$rules);


        foreach($this->_orm->registration_rules() as $field => $rules)
            $this->_vorm->rules($field,$rules);
        
        // si aggiunge il controllo della unicità della email
        $this->_vorm->rule('email',array($this->_orm, 'unique'), array('email', ':value'));
           
        
        // si aggiungono le rules per user_data
        foreach($this->_orm->user_data->rules() as $field => $rules)
            $this->_vorm->rules($field,$rules);
        
        foreach($this->_orm->user_data->extra_rules() as $field => $rules)
            $this->_vorm->rules($field,$rules);
        
        // add labels
        $this->_vorm->labels($this->_orm->labels());
        $this->_vorm->labels($this->_orm->user_data->labels());
        
          if(!$this->_vorm->check())
            $this->vErrors = Arr::push ($this->vErrors,$this->_vorm->errors('validation'));
        
        if(!empty($this->vErrors))
                throw new Validation_Exception($this->_vorm);
        
    }
    
    protected function _edit() {
        // we set rose to reporter
        $this->_roles[] = ORM::factory ('Role')->where('name','=','REPORTER')->find()->id;
        
        // we get new hash registration
        $_POST['hash_registration'] = ORM::factory('User')->build_hash_registration();
        //we set alse data_ins value
        $_POST['data_ins'] = time();
        
        $this->_giveLoginRole = FALSE;
        parent::_edit();
        
        if(isset($this->_orm->id) AND $this->_email_send)
        {
            Kohana::$log->add(LOG::DEBUG, 'HASH REGISTRATION: /confirmregistration/'.$this->_orm->hash_registration);
        
            // send email for confirm registration
            $email = new Email_Confirmregistration($this->_orm);
            $email->send();
            $this->after_save();
        }
        
        
    }
    
    public function after_save()
    {
        $this->jres->data = array(
            "actions" => array(
                "message" =>array(
                    'title' => 'Registrazione',
                    'content' => View::factory('messages/registrationok')->render(),
                    'type' => 'success',
                    'timeout' => 6000
                ),
            ),
        );
    }
    
  
}