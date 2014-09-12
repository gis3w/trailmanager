<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_I18n extends Controller_Ajax_Auth_Nostrict{
    
    public function action_index() {
        $this->jres->data = I18n::load($this->request->param('id'));
    }
}