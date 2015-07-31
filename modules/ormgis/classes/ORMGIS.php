<?php defined('SYSPATH') or die('No direct script access.');

class ORMGIS extends ORM {

    protected $geo_column = "the_geom";
    
    const TP_POLYGON = 'POLYGON';
    const TP_POINT = 'POINT';
    const TP_LINESTRING = 'LINESTRING';
    const TP_MULTIPOLYGON = 'MULTIPOLYGON';
    const TP_MULTIPOINT = 'MULTIPOINT';
    const TP_MULTILINESTRING = 'MULTILINESTRING';
    const TP_GEOMETRYCOLLECTION = 'GEOMETRYCOLLECTION';
    
    public $geotype = "POINT";
    
        // tipo di funzioni da applicare
    public static $geoFuncions = array(
        "astext" => "ST_AsText(%s)",
        "asgeojson" => "ST_AsGeoJson(%s)",
        "box2d" => "Box2D(%s)",
        "extent" => "ST_XMin(%1\$s) ||','|| ST_YMin(%1\$s) ||','|| ST_XMax(%1\$s) ||','|| ST_YMax(%1\$s)",
        "centroid" => "ST_AsText(ST_Centroid(%s))",
        "asbinary" => "ST_AsBinary(%s)",
    );
    
    public static $nogeoFuncions = array(
        
        
    );
    
    

    public static function factory($model, $id = NULL){
                
        $md = parent::factory($model);
        
        // si controlla che debba uscire in un'altro psg da quello di origine

        $the_geom = $md->geo_column;

        if($md->epsg_db !== $md->epsg_out){

            $the_geom = "ST_Transform(".$the_geom.",".$md->epsg_out.")";

        }
        
        foreach(self::$geoFuncions as $as => $exp)
        {
            $md->select(array(DB::expr(sprintf($exp, $the_geom)),$as));
        }
        
         
        foreach(self::$nogeoFuncions as $as => $exp)
        {
            $md->select(array(DB::expr(sprintf($exp, 'the_geom')),$as));
        }
        
        // nel caso di di punti singoli si aggiunge anche x e y
        if($md->geotype === self::TP_POINT)
        {
            
            $md->select(array(DB::expr("ST_X(".$the_geom.")"),'x'))
                    
                    ->select(array(DB::expr("ST_Y(".$the_geom.")"),'y'));
        }
        

        if ($id !== NULL)
		{
                if (is_array($id))
                {
                        foreach ($id as $column => $value)
                        {
                                // Passing an array of column => values
                                $md->where($column, '=', $value);
                        }

                        $md->find();
                }
                else
                {
                        // Passing the primary key
                        $md->where($md->object_name().'.'.$md->primary_key(), '=', $id)->find();
                }
        }

        return $md;
    }


    public function  __get($column) {

    switch ($column){

        case "bbox":
            $arr = array();
            // si fa il  parser del box2d e si recuprano il min e max dei vertici
            list($cornLB,$cornRT) = preg_split("/,/", substr($this->_object['box2d'], 4,-1));

            list($arr['minx'],$arr['miny']) = preg_split("/\ /",$cornLB);

            list($arr['maxx'],$arr['maxy']) = preg_split("/\ /",$cornRT);
            
            // forsiamo il tutto a float
            $arr = array_map(function($value){
                return (float)$value;
            }, $arr);

            return $arr;
            
        break;

         case "center":
            $arr = array();
            // si fa il  parser del box2d e si recuprano il min e max dei vertici
            list($arr['x'],$arr['y']) = preg_split("/,/", substr($this->_object['box2d'], 6,-1));

            return $arr;

        break;
    
        case "binarystring":
            // dobbiamo fare il rewind del puntatore
            rewind($this->_object['asbinary']);
            return stream_get_contents($this->_object['asbinary']);
        break;
    
        case 'lon':
            
            if(isset($this->x))
            {
                return (float)parent::__get('x');
            }
            else
            {
                // si fa la query e si recupra e si stocca nella _object
                $lonlat = $this->getLonLat();
                
                return (float)$this->x;
            }
            
                    
            
        break;
    
        case 'lat':
            
            if(isset($this->y))
            {
                return (float)parent::__get('y');
            }
            else
            {
                // si fa la query e si recupra e si stocca nella _object
                $lonlat = $this->getLonLat();
                
                return (float)$this->y;
            }
           
            
        break;
        
        case 'geo':
            if(!$this->pk())
                return NULL;
            $geo = GEO::load($this->astext,'wkt');
            $geo->setSRID($this->epsg_db);
            
            return $geo;
        break;
        
        case "asgeojson_php":
            return json_decode($this->asgeojson);
        break;
        
        case 'length_spheroid_km':
            // applicabile solo se il tipo è linestring
            if(!$this->pk())
                return NULL;
            $lenght= DB::select(
                array(DB::expr(TRKDB::float82numeric(TRKDB::lengh_spheroid_km('the_geom'))),'traveled_km')
                )
            ->from($this->_table_name)
            ->where($this->primary_key(),'=',(int)$this->pk())
            ->execute()
            ->as_array();
            
            return $lenght[0]['traveled_km'];
        break;
        
        
    
        

        default:
           return parent::__get($column);
    }



}

public function  __set($column,  $value) {

    switch ($column){

        case "the_geom":

            if($value instanceof Gisfeature){

                if (!isset($this->epsg_db)) $this->epsg_db = $this->getSrid();

                // si strasforma in wkt per il salvataggio
                $value = "ST_GeomFromText('".Gis_Util::toWkt($value->asGeoJson)."',".$this->epsg_out.")";

            }
            elseif(is_array($value))
            {
                //si passano i valori direttamente senga trasformazione da gejson
                if($this->geotype === self::TP_POINT)
                {
                    $points = implode(' ', $value);
                }
                $value = "ST_GeomFromText('".$this->geotype."(".$points.")',".$this->epsg_out.")";

                
               
            }
            elseif($value instanceof Geometry OR $value instanceof GeometryCollection)
            {
                // si controlla che abbia lo srid
                if(!is_null($value->SRID()))
                        $this->epsg_out = $value->SRID();
                
                $value = "ST_GeomFromText('".$value->asText()."',".$this->epsg_out.")";
            }
            elseif($value instanceof Database_Query_Builder )
            {
                // si controlla che abbia lo srid
//                if(!is_null($value->SRID()))
//                        $this->epsg_out = $value->SRID();
//                $value = "ST_GeomFromText('".$value->asText()."',".$this->epsg_out.")";
                $value = "(".$value.")";
            }
            elseif(is_string($value))
            {
                $value = "ST_GeomFromText('".$value."',".$this->epsg_out.")";
            }
            elseif(is_null($value))
            {
                $value = 'null';
            }
            
             if($this->epsg_db !== $this->epsg_out){

                $value = "ST_transform($value,".$this->epsg_db.")";
            }

            $value = DB::expr($value);

        break;
    }

    parent::__set($column, $value);

}


   /**
     * Recupera srid della tabella
     * @param <type> $tb
     * @return <type>
     */
  public function getSrid(){
     $srid = DB::select(array(DB::expr('st_srid(the_geom)'), 'srid'))
        ->from($this->_table_name)
        ->limit(1)
        ->execute($this->_db)
        ->get('srid');

     if(!is_null($srid)){
         return $srid;
     }else{
         // si fa la query su geometry_columns
        return DB::select()
        ->from("geometry_columns")
        ->where("f_table_name","=",$this->_table_name)
        ->execute($this->_db)
        ->get('srid');
     }
  }
  
  public function getLonLat($srid = NULL)
  {
      $sqlX = "ST_X(the_geom)";
      $sqlY = "ST_Y(the_geom)";
      if(isset($srid))
      {
          $sqlX = "ST_X(ST_Transform(the_geom,".$srid."))";
          $sqlY = "ST_Y(ST_Transform(the_geom,".$srid."))";
      }


      $res = DB::select(array(DB::expr($sqlX),'x'), array(DB::expr($sqlY),'y'))
        ->from($this->_table_name)
        ->where($this->_primary_key, '=', $this->pk())
        ->execute($this->_db);
      
      $this->_object['x'] = $res[0]['x'];
      $this->_object['y'] = $res[0]['y'];

  }

  
  public function find_all(){
      
      $params = func_get_args();
      $res = parent::find_all();
      
      // se non ci sono prametri si restituisce puro così comè
      if(empty($params))
          return $res;
      
      if(isset($params[0]) AND $params[0] !== 'geoJson')
          throw new HTTP_Exception_500('spiacente ma le modalità di uscita supportate sono: "geoJson"');
      
      if($params[0] === 'geoJson' AND !array_key_exists('geo',Kohana::modules()))
          throw new HTTP_Exception_500('non puoi utilizzare questa tipo di uscita ("geoJson") se non hai abilittato il modulo "geo"');
     
      
      switch($params[0])
      {
          case "geoJson":
              $filter = (isset($params[1]) AND is_array($params[1])) ? $params[1] : NULL;
              
              $fc = new Featurecollection($res,$filter);
             return $fc->as_geoJson();
          break;
      }
      
  }
  
  public function isValid()
  {
      $res = DB::select(DB::expr('ST_IsValid(the_geom)'))
                      ->from($this->_table_name)
                        ->where($this->_primary_key, '=', $this->pk())
                        ->execute($this->_db);
      
      return (bool)$res[0]['st_isvalid'];
  }
  
  public function __destruct() {
      $this->_db->setConfig('column_primary_key','id');
  }
}


