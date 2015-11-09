<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Subtablepoi extends Controller_Ajax_Admin_Poi{

    protected $_template = 'ajax/subtable/poi';

    public function action_create() {}

    public function action_update() {}

    public function action_delete() {}



    protected function _default_filter($orm)
    {
        if (!isset($_GET['path_id']))
            return;



        /*
         * Geographic filter:
         *  $path = ORMGIS::factory('Path',$_GET['path_id']);
            $geocond = "ST_Intersects(the_geom,ST_Buffer(ST_Transform(ST_GeometryFromText('".$path->astext."',4326),3004),10))";
            $orm->where(DB::expr($geocond),'IS',DB::expr('true'));
         */


        return $orm;

    }
}