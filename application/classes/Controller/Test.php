<?php defined('SYSPATH') or die('No direct script access.');

        
        use Symfony\Component\EventDispatcher\EventDispatcher;
        use Symfony\Component\EventDispatcher\Event;

class Controller_Test extends Controller{
    
    public function action_index(){

        
       $dispatcher = new EventDispatcher();
       $that = $this;
$dispatcher->addListener('event_name', array($this,'test'));

$dispatcher->dispatch('event_name');
       exit;
    
    }
    
    public function test($event)
    {
      var_dump($event);  
      var_dump($this);
    }
            
}