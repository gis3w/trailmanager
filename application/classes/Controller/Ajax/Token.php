<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Token extends Controller_Ajax_Main{
    
    public function action_index() {
            if(Auth::instance()->logged_in())
            {
                $this->jres->data->token = md5(Auth::instance()->get_user()->username.time());
            }
            else
            {
                $this->jres->data->token = md5('anonimous'.time());
            }
            
            // token name
            $token_name = isset($_GET['datastruct']) ? $_GET['datastruct'] : '';
            
            $token_name = 'token'.$token_name;
            
             //aggiornamento della sessione 
             Session::instance()->set($token_name,$this->jres->data->token);
    }
}
   