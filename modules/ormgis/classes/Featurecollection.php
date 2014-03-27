<?php defined('SYSPATH') or die('No direct script access.');

class Featurecollection extends Basefeature {

    public $type = "FeatureCollection";
    
    public $features = array();
    
    protected $_dbResults;


    public function __construct() {
        $params = func_get_args();
        
         if(isset($params[1]))
                $this->filterProperties = $params[1];
        
        if(!empty($params) AND (is_array($params[0]) OR $params[0] instanceof Database_Result))
        {
           
            $this->_dbResults = $params[0];
            $this->_fillFeatures();
        }
            
    }
    
    protected function _fillFeatures(){
        
        foreach ($this->_dbResults as $res)
        {
//            if(isset($res) AND $res instanceof ORMGIS)
//            {
                $this->addFeature(new Feature($res,$this->filterProperties));
//            }    
                
        }
    }
    
    public function addFeature(Feature $feature){
        
        $this->features[] = $feature;
        
    }

}
