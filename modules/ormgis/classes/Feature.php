<?php defined('SYSPATH') or die('No direct script access.');

class Feature extends Basefeature {

    public $type = "Feature";
    
    public $geometry;
    
    public $properties = array();
    
    protected $_orm;


    public function     __construct() {
        $params = func_get_args();
        
        if(isset($params[1]))
                $this->filterProperties = $params[1];
        
        if(!empty($params) AND $params[0] instanceof ORMGIS)
        {
            
            $this->_orm = $params[0];
            $this->setGeometry($this->_orm->geo);
            $this->_fillProperties();
        }
        elseif(!empty($params) AND $params[0] instanceof Geometry)
        {
            $this->setGeometry($params[0]);
            if(isset($params[1]) AND is_array($params[1]))
                $this->properties = $params[1];
        }
        elseif(!empty($params) AND is_array($params[0]) AND array_key_exists('wkt', $params[0]))
        {
            $this->setGeometry(GEO::load($params[0]['wkt'],'wkt'));
            unset($params[0]['wkt']);
            $this->properties = $params[0];
        }        
        elseif(!empty($params) AND is_array($params[0]))
        {
            $this->setGeometry($params[0]);
            if(isset($params[1]) AND is_array($params[1]))
                $this->properties = $params[1];
        }
    }
        
    protected function _fillProperties(){
        
        $values = $this->_orm->as_array();
        unset(
                $values['the_geom'],
                $values['astext'],
                $values['asgeojson'],
                $values['box2d'],
                $values['extent'],
                $values['centroid'],
                $values['x'],
                $values['y']
                );
        
        // poi si vanno a controllare eventuali
        // _belongs_to  has_many has_one
        foreach($this->_orm->belongs_to() as $col => $par)
        {
            // si elimina la colonna corrispondente e si mette quella da cui prende
            $values[$col] = (string)$this->_orm->$col;
            unset($values[$par['foreign_key']]);
        }
        
        //si filtrano le property
        if(!empty($this->filterProperties))
            $values = array_intersect_key ($values, array_flip ($this->filterProperties));
        
        $this->properties = $values;
    }
    
    public function setGeometry($geo){

        if($geo instanceof Geometry)
        {
            $geometry = array(
            'type'=> $geo->getGeomType(),
            'coordinates'=> $geo->getCoordinates()
            );
        }
        elseif(is_array($geo))
        {
            $geometry = array(
                'type'=> $geo[0],
                'coordinates'=> $geo[0]
            );
            
        }
        
        $this->geometry = $geometry;
            
    }   
        
    
    public function addProperty($key,$value){
        $this->properties[$key] = $value;
    }

}
