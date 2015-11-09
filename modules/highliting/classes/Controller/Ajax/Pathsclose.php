<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Pathsclose extends Controller_Ajax_Main{

    public function action_index() {

        // get the x and y coordinate
        $lon = $this->request->param('lon');
        $lat = $this->request->param('lat');

        $maxDistance = 100;

        if (!isset($lon) OR ! isset($lat))
            throw new HTTP_Exception_500(SAFE::message('ehttp','no_lon_or_lat_param'));
        $paths = ORMGIS::factory('Path');
        $geoPOINT = "ST_Transform(ST_SetSRID(ST_Point(".$lon.",".$lat."),4326),".$paths->epsg_db.")";

        // build distance query
        $paths = $paths->where(DB::expr("(ST_Distance(the_geom,".$geoPOINT."))"),'<=',$maxDistance)
            ->find_all();

        if(count($paths) >= 0)
            $this->jres->data->items=[];
        foreach($paths as $path)
        {
            $this->jres->data->items[]  = [
                'id' => $path->pk(),
                'nome' => $path->nome,
            ];
        }
    }

    }