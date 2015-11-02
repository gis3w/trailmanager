<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Heightsprofilepath extends Controller_Ajax_Base_GET{


    public function action_index() {
        // we get path id value
        $path_id = $this->request->param('id');
        if (!$path_id)
            throw new HTTP_Exception_500(SAFE::message('ehttp','500_no_path_id'));

        // try to retry path orm data
        $path = ORMGIS::factory('Path',$path_id);
        if (!$path->pk())
            throw new HTTP_Exception_500(SAFE::message('ehttp','500_path_orm'));

        //we get heights_profile data
        $heights_profile_data = $path->heights_profile->find_all()->as_array();
        // rebuild data for C3.js
        $chartdata = [
            ['x'],
            ['data-'.$path_id]
        ];
        $lhpd = count($heights_profile_data);
        switch($lhpd)
        {
            case $lhpd >= 1 AND $lhpd < 301:
                $limit_cont = 1;
            break;
            case $lhpd >= 300 AND $lhpd < 601:
                $limit_cont = 2;
            break;
            case $lhpd >= 600 AND $lhpd < 901:
                $limit_cont = 3;
            break;
            case $lhpd >= 900 AND $lhpd < 1201:
                $limit_cont = 4;
            break;
            case $lhpd >= 1200 AND $lhpd < 1501:
                $limit_cont = 5;
            break;
            case $lhpd >= 1500 AND $lhpd < 2001:
                $limit_cont = 10;
            break;
            case $lhpd >= 2000 AND $lhpd < 3001:
                $limit_cont = 10;
            break;
            case $lhpd >= 3000:
                $limit_cont = 20;
        }
        $cont = 0;
        foreach($heights_profile_data as $data)
        {
            $cont++;
            if ($cont == 1)
            {
                $chartdata[0][] = (float)$data->cds2d;
                $chartdata[1][] = (float)$data->z;
            }

            if($cont == $limit_cont)
                $cont = 0;

        }
        $this->jres->data = $chartdata;

    }
}
   