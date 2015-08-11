<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export_Main extends Controller_Auth_Strict
{
    
    public function before() {
        parent::before();
        
        $this->_ACL();
    }

    
    protected function _ACL()
    {
        $this->_directory_ACL();
       $this->_controller_ACL(); 
    }


    protected function _controller_ACL()
    {

        // recuper del controller
        $controller = strtolower(preg_replace("/_/", "-", $this->request->controller()));
        $directory = strtolower(preg_replace("/\//", "-", $this->request->directory()));

        $controller = $directory."-".$controller;

        if($this->request->action() === 'index' AND !$this->user->role->allow_capa($controller))
                throw HTTP_Exception::factory(403,TRK::message('capability','default',NULL,$controller));

        if($this->request->action() !== 'index' AND !$this->user->role->allow_capa($controller.'-'.$this->request->action()))
                throw HTTP_Exception::factory(403,TRK::message('capability','default',NULL,$controller.'-'.$this->request->action()));

    }
    
     protected function _directory_ACL()
    {
         if(!$this->user->role->allow_capa('access-export'))
                throw HTTP_Exception::factory(403,TRK::message('capability','default',NULL,'access-export'));
    }
    
    public function after() {
        
    }
   
}