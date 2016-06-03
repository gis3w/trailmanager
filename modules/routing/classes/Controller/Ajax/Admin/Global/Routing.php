<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Global_Routing extends Controller_Ajax_Auth_Strict{
    

    public function action_create()
    {
        $ps = ORMGIS::factory('Path_Single')->find_all();
        var_dump($ps);
        exit;
    }

    public function action_index()
    {
        $this->jres->data = 'test';

        # create paths single

        try
        {

            //test per geo
            Database::instance()->begin();


            $qs = "drop table if exists paths_single";
            DB::query(NULL, $qs)->execute();
            $qs = "drop table if exists paths_single_noded";
            DB::query(NULL, $qs)->execute();
            $qs = "drop table if exists paths_single_vertices_pgr";
            DB::query(NULL, $qs)->execute();
            $qs = "drop table if exists paths_single_noded_vertices_pgr";
            DB::query(NULL, $qs)->execute();
            $qs = "create table paths_single as (select id as path_id, (st_dump(st_linemerge(the_geom))).geom as the_geom from paths)";
            DB::query(NULL, $qs)->execute();
            $qs = "alter table paths_single add column id serial primary key";
            DB::query(NULL, $qs)->execute();
            $qs = "alter table paths_single add column source integer";
            DB::query(NULL, $qs)->execute();
            $qs = "alter table paths_single add column target integer";
            DB::query(NULL, $qs)->execute();
            $qs = "alter table paths_single add column cost float8 default 1";
            DB::query(NULL, $qs)->execute();
            $qs = "alter table paths_single alter column the_geom type geometry(LineString, 3004)";
            DB::query(NULL, $qs)->execute();
            

            # create topology and node netwrok
            $qs = "select pgr_createTopology('paths_single', 1)";
            $q = DB::query(NULL, $qs);
            $r = $q->execute();

            $qs = "select pgr_nodeNetwork('paths_single',1)";
            DB::query(NULL, $qs)->execute();
            $qs = "SELECT pgr_createTopology('paths_single_noded', 1)";
            DB::query(NULL, $qs)->execute();
            $qs = "alter table paths_single_noded add column cost float8 default 1";
            DB::query(NULL, $qs)->execute();


            Database::instance()->commit();
        }
        catch (Database_Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }

        
        


    }


}