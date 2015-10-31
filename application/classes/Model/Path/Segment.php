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
            "cod_f1" => __('Code f1'),
            "cod_f2" => __('Code f2'),
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
            'diff_current' => __('Difficulty typology path segment').' '.__('current'),
            'percorr_current' => __('Walkable path segment').' '.__('current'),
            'rid_perc_current' => __('Reduction walkable path segment').' '.__('current'),
            'cop_tel_current' => __('GSM coverage path segment').' '.__('current'),
        );
    }

    public function rules()
    {
        return array(
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