<?php defined('SYSPATH') or die('No direct script access.');

        
//        use Symfony\Component\EventDispatcher\EventDispatcher;
//        use Symfony\Component\EventDispatcher\Event;

class Controller_Test extends Controller{
    
    public function action_index(){

        //echo floatval('10,3456');
        $ORMType = 'ORM';
        $sd = ORMGIS::factory('Highliting_Poi');
        var_dump($sd);
        //var_dump($sd->considerable_points->find_all());

        //$sd = ORMGIS::factory('Considerable_Point')->find();
        //var_dump($sd);
    }
    
    
            
}