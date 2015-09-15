<?php defined('SYSPATH') or die('No direct script access.');

use CpChart\Services\pChartFactory;


class Controller_Test extends Controller{

    public function action_index(){

        #$poi = ORMGIS::factory('Poi',865);
        #$poi->getLonLat(3857);
        #$map = new Mapserver(10000,array($poi->x,$poi->y));
        #$printConfig = Kohana::$config->load('print');
        #,NULL,10000,array($poi->x,$poi->y)
        #$map = new Mapserver($printConfig['mapfile'],$printConfig['mappath'],$printConfig['tmp_dir']);
        #$map->makeMap();

        /*
        $path = ORMGIS::factory('Path',26);
*/

        /*
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



        $path = ORMGIS::factory('Path',26);
        $pt = $geo->pointFromToSRS([(int)$path->coordxini,(int)$path->coordyini],3004,3857);
         */

      $kml = new KMLF();
        echo $kml->render();
        exit;



    }



}