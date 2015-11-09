<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Path_Segment extends ORMGIS
{
    protected $_primary_key = 'gid';

    public $geotype = ORMGIS::TP_MULTILINESTRING;

    public $epsg_db = 3004;
    public $epsg_out = 4326;

    protected $_belongs_to = array(
        'class_ril_desc' => array(
            'model' => 'Class_Ril_Segment',
            'foreign_key' => 'class_ril',
        ),
        'tp_trat_desc' => array(
            'model' => 'Tp_Trat_Segment',
            'foreign_key' => 'tp_trat',
        ),

    );

    public function labels() {
        return array(
            "id_tratta" => __('ID Path segment'),
            "ex_se" => __("Ex Se"),
            "se" => __("Se"),
            "cod_f1" => __("Cod F1"),
            "cod_f2" => __("Cod F2"),
            "data_ril" => __('Survey date'),
            "condmeteo" => __('Weather state'),
            'rilev' => __('Data collector'),
            'class_ril' => __('Survey class'),
            'qual_ril' => __('Quality survey'),
            'tp_trat' => __('Typology path segment'),
            'tp_fondo' => __('Bottom typology path segment'),
            'diff' => __('Difficulty typology path segment'),
            'percorr' => __('Walkable path segment'),
            'morf' => __('Morfology path segment'),
            'ambiente' => __('Ambient path segment'),
            'cop_tel' => __('GSM coverage path segment'),
            'rid_perc' => __('Reduction walkable path segment'),
            "the_geom" => __('Geodata'),
        );
    }

    public function rules()
    {
        return array(
            'id_tratta' => array(
                array('not_empty'),
            ),
            'se' => array(
                array('not_empty'),
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