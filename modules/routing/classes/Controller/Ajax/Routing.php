<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Routing extends Controller_Ajax_Main{


    public function action_delete() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }

    public function action_update() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }

    public function action_create() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }

    public function action_index()
    {

        // get point from

        $fromPoint = preg_split('/,/',$_GET['from']);
        $fromPoint = $fromPoint[1].' '.$fromPoint[0];
        $toPoint = preg_split('/,/',$_GET['to']);
        $toPoint = $toPoint[1].' '.$toPoint[0];

        $fromPath = Model_Paths_Single_Noded::getPathByPoint($fromPoint);
        $toPath = Model_Paths_Single_Noded::getPathByPoint($toPoint);


        $positionFromPath = $fromPath->getPositionOnPath($fromPoint);
        $positionToPath = $toPath->getPositionOnPath($toPoint);

        // try to perfom routing
        $routingPaths = $fromPath->calculateRouting($positionFromPath, $toPath, $positionToPath);


        if (count($routingPaths) == 0)
        {
            $this->jres->data =[];
        }
        else
        {
            $toRes = [];
            for ($i = 0; $i < count($routingPaths); $i++)
            {
                $routingPath = $routingPaths[$i];
                if ($routingPath['psn_id'] == $fromPath->id OR $routingPath['psn_id'] == $toPath->id)
                {

                    if($routingPath['psn_id'] == $fromPath->id)
                    {
                        $routingPath2 = $routingPaths[$i + 1];
                        list($path, $position) = [$fromPath, $positionFromPath];
                        $toEnd = $routingPath['psn_target'] == $routingPath2['id2'];
                    }
                    else
                    {
                        $routingPathN1 = $routingPaths[$i - 1];
                        list($path, $position) = [$toPath, $positionToPath];
                        if ($routingPath['psn_target'] == $routingPathN1['psn_source'] OR
                            $routingPath['psn_target'] == $routingPathN1['psn_target']) {
                            $toEnd = True;
                        }
                        else
                        {
                            $toEnd = False;
                        }

                    }

                    $subPath = $path->getGeoJSONSubPathByFraction($position, $toEnd);

                    $toRes[] = [
                        "path_id" => $routingPath['path_id'],
                        "geoJSON" => json_decode($subPath['geojson_subapth']),
                        "length" => (float)$subPath['geojson_subapth_length']
                    ];
                }
                else
                {
                    $toRes[] = [
                        "path_id" => $routingPath['path_id'],
                        "geoJSON" => ORMGIS::factory('Paths_Single_Noded', $routingPath['psn_id'])->asgeojson_php,
                        "length" => $routingPath['length'] * $routingPath['cost']
                    ];
                }

            }
            $this->jres->data = $toRes;
        }

    }
}