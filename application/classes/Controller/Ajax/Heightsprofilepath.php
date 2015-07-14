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
        foreach($heights_profile_data as $data)
        {
            $chartdata[0][] = (float)$data->cds2d;
            $chartdata[1][] = (float)$data->z;
        }
        $this->jres->data = $chartdata;

    }
}
   