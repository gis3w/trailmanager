<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Subtablepath extends Controller_Ajax_Admin_Path{

    protected $_template = 'ajax/subtable/path';

    public function action_create() {}

    public function action_update() {}

    public function action_delete() {}



    protected function _default_filter($orm)
    {
        if (!isset($_GET['poi_id']) and !isset($_GET['path_segment_id']))
            return;
        if(isset($_GET['poi_id']))
        {
            $geodata = ORMGIS::factory('Poi',$_GET['poi_id']);
        }
        else
        {
            $geodata = ORMGIS::factory('Path_Segment',$_GET['path_segment_id']);
        }
        $geocond = "ST_Intersects(the_geom,ST_Buffer(ST_Transform(ST_GeometryFromText('".$geodata->astext."',4326),3004),10))";
        $orm->where(DB::expr($geocond),'IS',DB::expr('true'));
        return $orm;

    }
}