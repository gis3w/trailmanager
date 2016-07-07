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
        

        # create paths single
        $toResMesg = [];
        try
        {

            //test per geo
            Database::instance()->begin();

            $qs = "drop table if exists paths_single";
            DB::query(NULL, $qs)->execute();
            $toResMesg[] = "Delete 'paths_single' table";
            $qs = "drop table if exists paths_single_noded";
            DB::query(NULL, $qs)->execute();
            $toResMesg[] = "Delete 'paths_single_noded' table";
            $qs = "drop table if exists paths_single_vertices_pgr";
            DB::query(NULL, $qs)->execute();
            $toResMesg[] = "Delete 'paths_single_vertices_pgr' table";
            $qs = "drop table if exists paths_single_noded_vertices_pgr";
            DB::query(NULL, $qs)->execute();
            $toResMesg[] = "Delete 'paths_single_noded_vertices_pgr' table";
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
            $toResMesg[] = "CREATE 'paths_single' table";
            

            # create topology and node netwrok
            $qs = "select pgr_createTopology('paths_single', 100)";
            $q = DB::query(NULL, $qs);
            $r = $q->execute();
            $toResMesg[] = "CREATE routing topology on path_single table";

            $qs = "select pgr_nodeNetwork('paths_single',100)";
            DB::query(NULL, $qs)->execute();
            $toResMesg[] = "CREATE node network on path_single table";

            $qs = "SELECT pgr_createTopology('paths_single_noded', 100)";
            $toResMesg[] = "CREATE routing topology on path_single_noded table";
            DB::query(NULL, $qs)->execute();
            $qs = "alter table paths_single_noded add column cost float8 default 1";
            DB::query(NULL, $qs)->execute();


            Database::instance()->commit();

            $toResMesg[] = "<span class=\"bg-success\">ROUTING TOPOLOGY BUILD!</span>";

            $res = View::factory('make_routing_topology');
            $res->msgs = $toResMesg;
            $this->jres->data = $res->render();
        }
        catch (Database_Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }

        
        


    }


}