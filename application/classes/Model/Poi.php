<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Poi extends ORMGIS {
    
    public $geotype = ORMGIS::TP_POINT;
    
    public $epsg_db = 3004;
    public $epsg_out = 4326;
    
    
    protected $_has_many = array(
        'itineraries' => array(
            'model'   => 'Itinerary',
            'through' => 'itineraries_pois',
        ),
        'typologies' => array(
            'model'   => 'Typology',
            'through' => 'typologies_pois',
        ),
        'images' => array(
            'model'   => 'Image_Poi',
        ),
         'videos' => array(
            'model'   => 'Video_Poi',
        ),
        'urls' => array(
            'model'   => 'Url_Poi',
        ),
    );

    protected $_belongs_to = array(
        'typology' => array(
            'model' => 'Typology'
        ),
        'pt_inter_code' => array(
            'model'   => 'Pt_Inter_Poi',
            'foreign_key' => 'pt_inter',
            'far_key' => 'code'
        ),
        'strut_ric_code' => array(
            'model'   => 'Strut_Ric_Poi',
            'foreign_key' => 'strut_ric',
            'far_key' => 'code'
        ),
        'aree_attr_code' => array(
            'model'   => 'Aree_Attr_Poi',
            'foreign_key' => 'aree_attr',
            'far_key' => 'code'
        ),
        'insediam_code' => array(
            'model'   => 'Insediam_Poi',
            'foreign_key' => 'insediam',
            'far_key' => 'code'
        ),
        'pt_acqua_code' => array(
            'model'   => 'Pt_Acqua_Poi',
            'foreign_key' => 'pt_acqua',
            'far_key' => 'code'
        ),
        'pt_socc_code' => array(
            'model'   => 'Pt_Socc_Poi',
            'foreign_key' => 'pt_socc',
            'far_key' => 'code'
        ),
        'fatt_degr_code' => array(
            'model'   => 'Fatt_Degr_Poi',
            'foreign_key' => 'fatt_degr',
            'far_key' => 'code'
        ),
    );
    
    public function labels() {
        return array(
            "cod_f1" => __("Cod F1"),
            "cod_f2" => __("Cod F2"),
            "title" => __("Title"),
            "description" => __("Description"),
            "publish" => __("Published"),
            "typology_id" => __("Main typology"),
            "inquiry" => __('Request informations'),
            "max_scale" => __('Max scale'),
            "data_ril" => __('Survey date'),
            "condmeteo" => __('Weather state'),
            'rilev' => __('Data collector'),
            "photo" => __('Photo'),
            "note" => __('Note'),
            "note_man" => __('Manteinance note'),
            "qual_ril" => __('Role survey'),
            "class_ril" => __('Survey class'),
            "pt_inter" => __('Point of interest class'),
            "strut_ric" => __('Accomodation building class'),
            "aree_attr" => __('Equip area class'),
            "insediam" => __('Village class'),
            "pt_acqua" => __('Water point class'),
            "tipo_segna" => __('Signage type class'),
            "stato_segn" => __('Signage state class'),
            "fatt_degr" => __('Degeneration cause class'),
            "pt_socc" => __('Rescue point class'),
            "coin_in_fi" => __('Start-end coincidence class'),
            "prio_int" => __('Priority intervention class'),
            "nuov_segna" => __('New signage'),
            "quota" => __('Altitude'),
            "coord_x" => __('X coordinate'),
            "coord_y" => __('Y coordinate'),
            "data_ins" => __('Insert date'),
            "data_mod" => __('Update date'),
            "the_geom" => __('Geodata'),
            "id_palo" => __('Pole ID'),
            'quali_ril' => __('Quality survey'),
        );
    }
    
    
    public function rules()
    {
        return array(
            'idwp' => array(

                array('not_empty'),
                #array(array($this,'unique_for_se')),

            ),
            'se' => array(
                array('not_empty'),
            ),
            'publish' =>array(
                    array('not_empty'),
            ),
             'max_scale' =>array(
                    array('numeric'),
            ),
            'the_geom' =>array(
                array('not_empty'),
            ),
        );
    }

    public function insert_rules()
    {
        return array(
            'idwp' => array(
                array(array($this,'unique_for_se')),
            ),
        );
    }


    public function filters()
    {
        return array(

            'coord_x' => array(
                array('Filter::comma2point')
            ),
            'coord_y' => array(
                array('Filter::comma2point')
            ),




        );
    }

    public function get($column) {

        switch($column)
        {

            case "coord_x":
            case "coord_y":
                $value = Filter::point2comma((string)parent::get($column));
                break;

            case "paths":
                $value = ORMGIS::factory('Path')->where('se','=',$this->se);
                break;

            default:
                $value = parent::get($column);

        }
        return $value;

    }

    public function unique_for_se($value)
    {
        $oldIdwp = (bool) count(DB::select()
            ->from($this->table_name())
            ->where('idwp','=',$value)
            ->where('se','=',$_POST['se'])
            ->execute());

        return !$oldIdwp;
    }

}