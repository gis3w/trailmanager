<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Home extends Controller_Base_Main {
    
    public $tcontent ="home";
    public $jspre = "BOOTSTRAP_URL='/jx/config'";
    public $main_menu_name = 'menu_frontend';

    public function action_index(){
        
        // aggiungiamo alla barra in altro il form di ricerca
        $this->tnavbar->search = TRUE;
        
        // si imposta un menu diffeerente
        $this->main_menu = $this->_get_main_menu();
        
    }
    
}