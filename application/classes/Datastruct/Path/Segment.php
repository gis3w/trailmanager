<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Path_Segment extends Datastruct
{
    protected $_nameORM = "Path_Segment";
    protected $_typeORM = "ORMGIS";

    public $icon = 'location-arrow';
    public $filter = FALSE;

    public $groups = array(
        array(
            'name' => 'path-base-data-path',
            'position' => 'left',
            'fields' => array('gid','se','bike','ip','cod_f1','cod_f2'),
        ),
        array(
            'name' => 'path-base-data-survey',
            'position' => 'left',
            'fields' => array('data_ril','condmeteo','rilev','qual_ril','class_ril'),
        ),
        array(
            'name' => 'path-base-data-geodata',
            'position' => 'right',
            'fields' => array('the_geom'),
        ),
        array(
            'name' => 'path-base-data-data',
            'position' => 'block',
            'fields' => array('tp_trat','tp_fondo','diff','percorr','rid_perc','morf','ambiente','cop_tel'),
        ),
    );

    protected function _columns_type()
    {

        return array(
            "se" => array(
                'editable' => FALSE,
            ),
            "bike" => array(
                'editable' => FALSE,
            ),
            "ip" => array(
                'editable' => FALSE,
            ),
            "cod_f1" => array(
                'editable' => FALSE,
            ),
            "cod_f2" => array(
                'editable' => FALSE,
            ),
            "data_ril" => array(
                'editable' => FALSE,
            ),
            "condmeteo" => array(
                'editable' => FALSE,
            ),
            "rilev" => array(
                'editable' => FALSE,
            ),
            "qual_ril" => array(
                'editable' => FALSE,
            ),
            "class_ril" => array(
                'editable' => FALSE,
                'form_input_type' => self::SELECT,
                'foreign_mode' => self::SINGLESELECT,
                'foreign_value_field' => 'class',
                'foreign_toshow' => '$1',
                'foreign_toshow_params' => array(
                    '$1' => 'description',
                ),
                'foreign_key' => 'class_ril_segment',
                'label' => __('Survey class'),
                "table_show" => TRUE,
            ),
            "tp_trat" => array(
                'editable' => FALSE,
                'form_input_type' => self::SELECT,
                'foreign_mode' => self::SINGLESELECT,
                'foreign_value_field' => 'code',
                'foreign_toshow' => '$1',
                'foreign_toshow_params' => array(
                    '$1' => 'description',
                ),
                'foreign_key' => 'tp_trat_segment',
                'label' => __('Typology path segment'),
                "table_show" => TRUE,
            ),
            "tp_fondo" => array(
                'editable' => FALSE,
                'form_input_type' => self::SELECT,
                'foreign_mode' => self::SINGLESELECT,
                'foreign_value_field' => 'code',
                'foreign_toshow' => '$1',
                'foreign_toshow_params' => array(
                    '$1' => 'description',
                ),
                'foreign_key' => 'tp_fondo_segment',
                'label' => __('Bottom typology path segment'),
                "table_show" => TRUE,
            ),
            "diff" => array(
                'editable' => FALSE,
                'form_input_type' => self::SELECT,
                'foreign_mode' => self::SINGLESELECT,
                'foreign_value_field' => 'code',
                'foreign_toshow' => '$1',
                'foreign_toshow_params' => array(
                    '$1' => 'description',
                ),
                'foreign_key' => 'diff_segment',
                'label' => __('Difficulty typology path segment'),
                "table_show" => TRUE,
            ),
            "percorr" => array(
                'editable' => FALSE,
                'form_input_type' => self::SELECT,
                'foreign_mode' => self::SINGLESELECT,
                'foreign_value_field' => 'code',
                'foreign_toshow' => '$1',
                'foreign_toshow_params' => array(
                    '$1' => 'description',
                ),
                'foreign_key' => 'percorr_segment',
                'label' => __('Walkable path segment'),
                "table_show" => TRUE,
            ),
            "morf" => array(
                'editable' => FALSE,
                'form_input_type' => self::SELECT,
                'foreign_mode' => self::SINGLESELECT,
                'foreign_value_field' => 'code',
                'foreign_toshow' => '$1',
                'foreign_toshow_params' => array(
                    '$1' => 'description',
                ),
                'foreign_key' => 'morf_segment',
                'label' => __('Morfology path segment'),
                "table_show" => TRUE,
            ),
            "ambiente" => array(
                'editable' => FALSE,
                'form_input_type' => self::SELECT,
                'foreign_mode' => self::SINGLESELECT,
                'foreign_value_field' => 'code',
                'foreign_toshow' => '$1',
                'foreign_toshow_params' => array(
                    '$1' => 'description',
                ),
                'foreign_key' => 'ambiente_segment',
                'label' => __('Ambient path segment'),
                "table_show" => TRUE,
            ),
            "cop_tel" => array(
                'editable' => FALSE,
                'form_input_type' => self::SELECT,
                'foreign_mode' => self::SINGLESELECT,
                'foreign_value_field' => 'code',
                'foreign_toshow' => '$1',
                'foreign_toshow_params' => array(
                    '$1' => 'description',
                ),
                'foreign_key' => 'cop_tel_segment',
                'label' => __('GSM coverage path segment'),
                "table_show" => TRUE,
            ),
            "rid_perc" => array(
                'editable' => FALSE,
                'form_input_type' => self::SELECT,
                'foreign_mode' => self::SINGLESELECT,
                'foreign_value_field' => 'code',
                'foreign_toshow' => '$1',
                'foreign_toshow_params' => array(
                    '$1' => 'description',
                ),
                'foreign_key' => 'rid_perc_segment',
                'label' => __('Reduction walkable path segment'),
                "table_show" => TRUE,
            ),
            "the_geom" => array(
                'form_input_type' => self::MAPBOX,
                'map_box_editing' => TRUE,
                'map_box_editing_geotype' => array(
                    self::GEOTYPE_POLYLINE
                ),
                'map_box_fileloading' => TRUE,
                'label' =>__('Geodata'),
                'table_show' => FALSE,
            ),
        );
    }
}