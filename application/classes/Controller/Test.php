<?php defined('SYSPATH') or die('No direct script access.');



class Controller_Test extends Controller{

    public function action_index(){

        #$poi = ORMGIS::factory('Poi',865);
        #$poi->getLonLat(3857);
        #$map = new Mapserver(10000,array($poi->x,$poi->y));
        $printConfig = Kohana::$config->load('print');
        #,NULL,10000,array($poi->x,$poi->y)
        #$map = new Mapserver($printConfig['mapfile'],$printConfig['mappath'],$printConfig['tmp_dir']);
        #$map->makeMap();


        $path = ORMGIS::factory('Path',26);

        $geo = GEO_Postgis::instance();
        $extent = [
            $path->bbox['minx'],
            $path->bbox['miny'],
            $path->bbox['maxx'],
            $path->bbox['maxy']
        ];

        $newExtent = $geo->bboxFromToSRS($extent,$path->epsg_out,3857);


        $map = new Mapserver($printConfig['mapfile'],$printConfig['mappath'],$printConfig['tmp_dir'],NULL,NULL,NULL,$newExtent);
        $map->makeMap();
        echo $map->imageURL;
        # Mapserver::makePoisSymbols();
        exit;



    }



}