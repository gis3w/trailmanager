<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Paths_Single_Noded extends ORMGIS
{
    public $geotype = ORMGIS::TP_LINESTRING;
    public $epsg_db = 3004;
    public $epsg_out = 4326;

    protected $_table_name = 'paths_single_noded';

    public static function getPathByPoint($points)
    {
        $path = DB::select(
            'id',
            [DB::expr("ST_Distance(the_geom, ST_Transform(ST_GeomFromText('POINT(".$points.")', 4326),3004))"), 'dist']
        )
            ->from('paths_single_noded')
            ->order_by('dist','ASC')
            ->limit(1)
            ->execute();

        return ORMGIS::factory('Paths_Single_Noded', $path[0]['id']);
    }

    public function getPositionOnPath($points)
    {
        #get percentage position on path
        $query =  DB::select(
            [DB::expr("ST_Line_Locate_Point(the_geom, ST_Transform(ST_GeomFromText('POINT(".$points.")', ".$this->epsg_out."),".$this->epsg_db."))"), 'position_on_path']
        )
            ->from($this->_table_name)
            ->where('id', '=', $this->id)
            ->execute();
        return $query[0]['position_on_path'];
    }

    public function getGeoJSONSubPathByFraction($fraction, $toEnd = TRUE)
    {
        # select direction
        if ($toEnd)
        {
            $subString = "ST_Line_Substring(the_geom, ".$fraction."::float, 1)";
        }
        else
        {
            $subString = "ST_Line_Substring(the_geom, 0, ".$fraction."::float)";
        }


        #get percentage position on path
        $query =  DB::select(
            [DB::expr("ST_AsGeoJSON(ST_Transform(".$subString.", ".$this->epsg_out."))"), 'geojson_subapth'],
            [DB::expr("ST_Length(".$subString.")"), 'geojson_subapth_length']
        )
            ->from($this->_table_name)
            ->where('id', '=', $this->id)
            ->execute();
        return $query[0];
    }

    public function calculateRouting($from_position, $to, $to_position)
    {
        $qrouting = "SELECT * FROM pgr_trspViaEdges(
        'SELECT id::INTEGER, source::INTEGER, target::INTEGER, cost
        FROM paths_single_noded',
        ARRAY[".$this->id.", ".$to->id."]::INTEGER[],           
        ARRAY[".$from_position.", ".$to_position."]::FLOAT[],     
        false,  
        false) r
        
        LEFT JOIN (select id as psn_id, old_id as psn_old_id, source as psn_source, target as psn_target, st_length(the_geom) as length from paths_single_noded) psn on (r.id3 = psn.psn_id)
        LEFT JOIN (select id as ps_id, path_id from paths_single) ps on (ps.ps_id = psn.psn_old_id);
        ";

        return DB::query(Database::SELECT, $qrouting)->as_assoc()->execute();
    }
}