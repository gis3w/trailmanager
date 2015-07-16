<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Pathsclose extends Controller_Ajax_Main{

    public function action_index() {

        // get the x and y coordinate
        $lon = $this->request->param('lon');
        $lat = $this->request->param('lat');

        if (!isset($lon) OR ! isset($lat))
            throw new HTTP_Exception_500(SAFE::message('ehttp','no_lon_or_lat_param'));


    }

    }