<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Subtablepathsegment extends Controller_Ajax_Admin_Pathsegment{

    protected $_template = 'ajax/subtable/pathsegment';

    public function action_create() {}

    public function action_update() {}

    public function action_delete() {}



    protected function _default_filter($orm)
    {
        if (!isset($_GET['poi_id']))
            return $orm;

        if (isset($_GET['poi_id']))
        {
            $poi = ORMGIS::factory('Poi',$_GET['poi_id']);
            $geocond = "ST_Intersects(the_geom,ST_Buffer(ST_Transform(ST_GeometryFromText('".$poi->astext."',4326),3004),50))";
            $orm->where(DB::expr($geocond),'IS',DB::expr('true'));
        }
        return $orm;

    }

}