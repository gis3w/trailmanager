<?php defined('SYSPATH') OR die('No direct access allowed.');

class Kohana_GEO_Postgis extends GEO{
    
    
    public function intersection(Geometry $feat1,Geometry $feat2)
    {
        $query = DB::query(Database::SELECT, "SELECT ST_AsText(ST_Intersection(ST_GeomFromText(:feat1),ST_GeomFromText(:feat2)))");
        
        $query->param(':feat1', $feat1->out('wkt'))
                ->param(':feat2', $feat2->out('wkt'));
        
        $res = $query->execute();
        
        return static::load($res[0]['st_astext'],'wkt');
    }
    
    public function intersects(Geometry $feat1,Geometry $feat2)
    {
        $query = DB::query(Database::SELECT, "SELECT ST_Intersects(ST_GeomFromText(:feat1),ST_GeomFromText(:feat2))");
        
        $query->param(':feat1', $feat1->out('wkt'))
                ->param(':feat2', $feat2->out('wkt'));
        
        $res = $query->execute();
        
        return $res[0]['st_intersects'] == 't' ? TRUE : FALSE ;
    }

    public function bboxFromToSRS($bbox, $fromESPG, $toEPSG )
    {
        $query = DB::query(Database::SELECT, "SELECT Box2D(ST_Transform(ST_SetSRID(ST_MakeBox2D(ST_POINT(:xmin,:ymin),ST_Point(:xmax,:ymax)),:fromepsg),:toepsg))");
        $query->param(':xmin', $bbox[0])
            ->param(':ymin', $bbox[1])
            ->param(':xmax', $bbox[2])
            ->param(':ymax', $bbox[3])
            ->param(':fromepsg', $fromESPG)
            ->param(':toepsg', $toEPSG);

        $res = $query->execute();


        $arr = array();
        // si fa il  parser del box2d e si recuprano il min e max dei vertici
        list($cornLB,$cornRT) = preg_split("/,/", substr($res[0]['box2d'], 4,-1));

        list($arr['minx'],$arr['miny']) = preg_split("/\ /",$cornLB);

        list($arr['maxx'],$arr['maxy']) = preg_split("/\ /",$cornRT);

        // forsiamo il tutto a float
        $arr = array_map(function($value){
            return (float)$value;
        }, $arr);

        return $arr;
    }
}
