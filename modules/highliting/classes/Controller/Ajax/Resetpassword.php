<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Resetpassword extends Controller_Ajax_Main{
    
    protected $_vorm;
    public $vErrors = array();


    public function action_update()
    {
         try
        {
             // try to perfom login
            $this->_validation();
            
            $user = ORM::factory('User')
                    ->where('email','=',$_POST['email'])
                    ->find();
            
            $mail = new Email_Resetpassword($user);
            Kohana::$log->add(LOG::DEBUG, $mail->getBodyMail());
            $mail->send();
           
        }
        catch (Validation_Exception $e)
        {
            $this->_validation_error($this->vErrors); 
        }
      
    }
    
     protected function _validation()
    {
        // oltre alla non empty di dpi e mansioni è necessario
        // validare gli indroci per unità produttiva che non si devono sovrapporre ??? chiedere
        
            $this->_vorm = Validation::factory($_POST);
            
            $this->_vorm->rules('email',array(
                array('not_empty'),
                array(array($this,'email_in_db')),
               
            ));
        
        
          if(!$this->_vorm->check())
            $this->vErrors = Arr::push ($this->vErrors,$this->_vorm->errors('validation'));
        
        if(!empty($this->vErrors))
                throw new Validation_Exception($this->_vorm);
        
    }
    
    public function email_in_db($email)
    {
        return (bool)count(
                ORM::factory('User')
                ->where('email','=',$email)
                ->find_all()
                );
    }
}