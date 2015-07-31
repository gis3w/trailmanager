<?php defined('SYSPATH') or die('No direct script access.');

/**
 *  Controller for confirm registration
 */

class Controller_Confirmregistration extends Controller_Base_Main {
    
    public $tcontent = 'confirmregistration';
    public $hash_registration;
    
     public function action_index()
    {
         $this->hash_registration = $this->tpl->tcontent->hash_registration = $this->request->param('hash_registration');
         $this->tpl->tcontent->user = ORM::factory('User')
                 ->where('hash_registration','=',$this->hash_registration)
                 ->find();
         
         $this->tpl->tcontent->registration = FALSE;
         if(isset($this->tpl->tcontent->user->id))
         {
             $this->tpl->tcontent->user->add('roles',ORM::factory('Role')->where('name','=','login')->find());
             $this->tpl->tcontent->registration = TRUE;
         }
    }
}