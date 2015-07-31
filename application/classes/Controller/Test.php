<?php defined('SYSPATH') or die('No direct script access.');



class Controller_Test extends Controller{

    public function action_index(){

        #$poi = ORMGIS::factory('Poi',867);
        #$poi->getLonLat(3857);
        #$map = new Mapserver(10000,array($poi->x,$poi->y));
        $printConfig = Kohana::$config->load('print');

        $map = new Mapserver($printConfig['mapfile'],$printConfig['mappath'],$printConfig['tmp_dir']);
        $map->makeMap();
        #echo $map->imageURL;
        # Mapserver::makePoisSymbols();
        exit;



    }



}