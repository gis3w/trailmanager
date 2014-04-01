<?php defined('SYSPATH') or die('No direct script access.');

        
        use Symfony\Component\EventDispatcher\EventDispatcher;
        use Symfony\Component\EventDispatcher\Event;

class Controller_Test extends Controller{
    
    public function action_index(){

        $poi = ORM::factory('Typology');
   var_dump($poi);
       exit;
    
    }
    
    
            
}