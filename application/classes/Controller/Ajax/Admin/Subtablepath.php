<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Subtablepath extends Controller_Ajax_Admin_Path{

    protected $_template = 'ajax/subtable/path';

    public function action_create() {}

    public function action_update() {}

    public function action_delete() {}



    protected function _default_filter($orm)
    {
        if (!isset($_GET['poi_id']))
            return;

        $poi = ORMGIS::factory('Poi',$_GET['poi_id']);
        $geocond = "ST_Intersects(the_geom,ST_Buffer(ST_Transform(ST_GeometryFromText('".$poi->astext."',4326),3004),10))";
        $orm->where(DB::expr($geocond),'IS',DB::expr('true'));

        return $orm;

    }
}