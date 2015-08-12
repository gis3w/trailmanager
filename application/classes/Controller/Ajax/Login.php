<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Login extends Controller_Ajax_Main{
    
    protected $_vorm;
    public $vErrors = array();


    public function action_update()
    {
        try
        {
             // try to perfom login
            $this->_validation();
            if(Auth::instance()->login($_POST['username'],$_POST['password'],false))
           {
                $this->jres->data->authuser = Controller_Ajax_Admin_User::user_data_plus(Auth::instance()->get_user());

            }else{
                  $this->_validation_error(array(
                      'username' => 'Username e/o password errati',
                      'password' => 'Username e/o password errati'
                      ));
            }
        }
        catch (Validation_Exception $e)
        {
            $this->_validation_error($this->vErrors,10000,'Authentication error');
            
        }
      

    }
    
     protected function _validation()
    {
        // oltre alla non empty di dpi e mansioni è necessario
        // validare gli indroci per unità produttiva che non si devono sovrapporre ??? chiedere
        
            $this->_vorm = Validation::factory($_POST);
            
            $this->_vorm->rules('username',array(
                array('not_empty'),
               
            ));
            
            $this->_vorm->rules('password',array(
                array('not_empty'),
               
            ));
        
        
          if(!$this->_vorm->check())
            $this->vErrors = Arr::push ($this->vErrors,$this->_vorm->errors('validation'));
        
        if(!empty($this->vErrors))
                throw new Validation_Exception($this->_vorm);
        
    }
}