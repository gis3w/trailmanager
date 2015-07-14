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
        $this->jres->data = $path->heights_profile->find_all()->as_array();

    }
}
   