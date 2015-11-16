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

    public function get($column) {

        switch($column)
        {

            case "paths":
                $value = ORMGIS::factory('Path')->where('se','=',$this->se);
                break;

            default:
                $value = parent::get($column);

        }
        return $value;

    }

}