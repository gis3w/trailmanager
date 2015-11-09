<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Path extends ORMGIS {
    
    public $geotype = ORMGIS::TP_MULTILINESTRING;
    
    public $epsg_db = 3004;
    public $epsg_out = 4326;

    protected $_belongs_to = array(
        'difficulty' => array(
            'model'   => 'Diff_Segment',
            'foreign_key' => 'diff',
        ),
        'walkable' => array(
            'model'   => 'Percorr_Segment',
            'foreign_key' => 'percorr',
        ),
    );
    
     protected $_has_many = array(
        'itineraries' => array(
            'model'   => 'Itinerary',
            'through' => 'itineraries_paths',
        ),
        'typologies' => array(
            'model'   => 'Typology',
            'through' => 'typologies_paths',
        ),
         'images' => array(
            'model'   => 'Image_Path',
        ),
          'videos' => array(
            'model'   => 'Video_Path',
        ),
         'modes' => array(
            'model'   => 'Path_Mode',
            'through' => 'path_modes_paths',
            'far_key' => 'path_mode_id'
        ),
         'heights_profile' => array(
             'model'   => 'Heights_Profile_Path',
         ),
         'urls' => array(
            'model'   => 'Url_Path',
        ),
    );
    
    public function labels() {
        return array(
            "descriz" => __("Description"),
            "diff_q" => __("Altitude gap"),
            "l" =>__("Length"),
            "publish" => __("Published"),
            "color" => __("Color"),
            "width" => __("Width"),
            "loc" => __("Cross places"),
            "cod_f1" => __("Cod F1"),
            "cod_f2" => __("Cod F2"),
            "ex_se" => __("Ex Se"),
            "se" => __("Se"),
            "the_geom" => __('Geodata'),
        );
    }
    
    
    public function rules()
    {
        return array(
            'nome' => array(
                    array('not_empty'),
            ),
            'se' => array(
                array('not_empty'),
            ),
            'l' => array(
                    array('numeric')
            ),
            'diff_q' => array(
                    array('numeric')
            ),
            'publish' =>array(
                    array('not_empty'),
            ),
            'the_geom' =>array(
                array('not_empty'),
            ),
            
        );
    }
    
     public function filters()
    {
        return array(
          
            'length' => array(
                    array('Filter::comma2point')
            ),
            'altitude_gap' => array(
                    array('Filter::comma2point')
            ),
            
            
        );
    }
    
    public function get($column) {
        
        switch($column)
        {
            case "l":
            case "dff_q":
                $value = Filter::point2comma((string)parent::get($column));
            break;

            case "considerable_points":
                $value = ORMGIS::factory('Considerable_Point')->where('se','=',$this->se);
            break;

            case "segments":
                $value = ORMGIS::factory('Path_Segment')->where('se','=',$this->se);
            break;
        
            default:
                $value = parent::get($column);
        }
        return $value;
        
    }

    public function getWaypoints()
    {
        #get vertexs of line
        $dumpPoints = DB::select(
            [DB::expr('ST_DumpPoints(the_geom)'), 'dp']
        )
            ->from('paths')
            ->where('id', '=', $this->id);


        return DB::select(
            [DB::expr('(dp).path[1]'), 'edge_id'],
            [DB::expr('ST_X(ST_Transform((dp).geom,' . $this->epsg_out . '))'), 'lon'],
            [DB::expr('ST_Y(ST_Transform((dp).geom,' . $this->epsg_out . '))'), 'lat']
        )
            ->from([$dumpPoints, 'foo'])
            ->execute();

    }

}