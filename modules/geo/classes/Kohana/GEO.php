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
    
    public function asGEOSMS()
    {
        
    }
    
   
    
    
    public static function featurecollection2wkt ($featurecollection){
        
        $wkts = array();
        
        foreach($featurecollection->features as $feature)
        {
            if(isset($feature->geometry) AND $feature->geometry->type !== 'FeatureCollection')
            {
                $geo  = GEO::load(json_encode($feature->geometry),'json');
                $wkts[] = $geo->asText();
            }
            else
            {
                $sub = self::featurecollection2wkt($feature->geometry);
                foreach($sub as $g)
                    $wkts[] = $g;
            }
                
        }
        
        return $wkts;
        
    }
    
}
