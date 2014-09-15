<?php defined('SYSPATH') or die('No direct script access.');

        
//        use Symfony\Component\EventDispatcher\EventDispatcher;
//        use Symfony\Component\EventDispatcher\Event;

class Controller_Test extends Controller{
    
    public function action_index(){

        //echo floatval('10,3456');
        $ORMType = 'ORM';
        $sd = ORM::factory('Search_Data')->find_all();
        var_dump($sd[0]->getElement());
    
    }
    
    
            
}