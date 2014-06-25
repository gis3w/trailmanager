<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Download_Base extends Controller_Admin_Download_Base {
    
    public function before() {
        $this->_initialize();
    }
}