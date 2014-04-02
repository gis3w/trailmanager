<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Token extends Controller_Ajax_Auth_Strict{


    public function action_index() {
             $this->jres->data->token = md5($this->user->username.time());
             //aggiornamento della sessione 
             $this->session->set('token',$this->jres->data->token);
    }
}
   