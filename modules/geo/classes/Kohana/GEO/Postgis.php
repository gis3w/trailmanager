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
}
