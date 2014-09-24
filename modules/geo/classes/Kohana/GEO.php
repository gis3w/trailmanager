<?php defined('SYSPATH') OR die('No direct access allowed.');


class Kohana_GEO extends geoPHP{
    
    /**
     * Driver che sta usando al momento
     * @var String 
     */
    public $driver;
    
    public static function instance() {
        
        // Load the configuration for this type
        $config = Kohana::config('geophp');

        if ( ! $type = $config->get('driver'))
        {
                $type = 'postgis';
        }

        // Set the session class name
        $class = 'GEO_'.ucfirst($type);
        
        $obj = new $class;
        
        $obj->driver = $type;

        // Create a new session instance
        return $obj;
        
    }
    
    public static function PostgisGentroid($geometry)
    {
        $res = DB::query(Database::SELECT, "Select astext(ST_Centroid(ST_GeomFromText('".$geometry->asText()."')))")
                ->execute();
        return self::load($res['0']['astext'],'wkt');
    }


    
    
    public static function featurecollection2format ($featurecollection,$format = 'wkt'){
        
        $wkts = array();
        
        
        foreach($featurecollection->features as $feature)
        {
            if(isset($feature->geometry) AND $feature->geometry->type !== 'FeatureCollection')
            {
                switch($format)
                {
                    case "wkt":
                        $method = 'asText';
                    break;
                
                    case "php":
                        $method = 'getGeoInterface';
                    break;
                }
                $geo  = GEO::load(json_encode($feature->geometry),'json');

                if(in_array(strtoupper($geo->getGeomType()),array('MULTILINESTRING','MULTIPOLYGON','MULTIPOINT')))
                {
                    $components = $geo->getComponents();
                    foreach($components as $geometry)
                        $wkts[] = $geometry->$method();
                }
                else
                {
                  $wkts[] = $geo->$method();  
                }
                
               
            }
            else
            {
                $sub = self::featurecollection2format($feature->geometry,$format);
                foreach($sub as $g)
                    $wkts[] = $g;
            }
                
        }
        
        return $wkts;
        
    }
    
    public static function featurecollection2wkt ($featurecollection){
        return self::featurecollection2format($featurecollection, 'wkt');
    }
    
    public static function featurecollection2php($featurecollection){
        return self::featurecollection2format($featurecollection, 'php');
    }
    
}
