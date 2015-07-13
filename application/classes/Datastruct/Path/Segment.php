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
            'name' => 'path-base-data-current',
            'position' => 'right',
            'fields' => array('percorr_current','rid_perc_current','cop_tel_current'),
        ),
        array(
            'name' => 'path-base-data-data',
            'position' => 'block',
            'fields' => array('tp_trat','tp_fondo','diff','percorr','rid_perc','morf','ambiente','cop_tel'),
        ),
        array(
            'name' => 'poi-base-data-paths',
            'position' => 'block',
            'fields' => array(
                'paths_path_segment',
            ),
        ),
    );

    public $tabs = array(
        array(
            'name' => 'tab-main',
            'icon' => 'mobile-phone',
            'groups' => array('path-base-data-path','path-base-data-survey','path-base-data-geodata','path-base-data-current','path-base-data-data'),
        ),
        array(
            'name' => 'tab-path-segment-path',
            'icon' => 'location-arrow',
            'groups' => array('poi-base-data-paths'),
        )
    );


    protected function _columns_type()
    {

        $baseSingleSelectField = array(
            'editable' => TRUE,
            'form_input_type' => self::SELECT,
            'foreign_mode' => self::SINGLESELECT,
            'foreign_value_field' => 'code',
            'foreign_toshow' => '$1',
            'foreign_toshow_params' => array(
                '$1' => 'description',
            ),
            "table_show" => TRUE,
        );

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

            "class_ril" => array_replace($baseSingleSelectField,array(
                'foreign_key' => 'class_ril_segment',
                'label' => __('Survey class'),
                'foreign_value_field' => 'class',
            )),

            "tp_trat" => array_replace($baseSingleSelectField,array(
                'foreign_key' => 'tp_trat_segment',
                'label' => __('Typology path segment'),
            )),

            "tp_fondo" => array_replace($baseSingleSelectField,array(
                'foreign_key' => 'tp_fondo_segment',
                'label' => __('Bottom typology path segment'),
            )),

            "diff" => array_replace($baseSingleSelectField,array(
                'foreign_key' => 'diff_segment',
                'label' => __('Difficulty typology path segment'),
            )),

            "percorr" => array_replace($baseSingleSelectField,array(
                'foreign_key' => 'percorr_segment',
                'label' => __('Walkable path segment'),
            )),

            "morf" => array_replace($baseSingleSelectField,array(
                'foreign_key' => 'morf_segment',
                'label' => __('Morfology path segment'),
            )),

            "ambiente" => array_replace($baseSingleSelectField,array(
                'foreign_key' => 'ambiente_segment',
                'label' => __('Ambient path segment'),
            )),

            "cop_tel" => array_replace($baseSingleSelectField,array(
                'foreign_key' => 'cop_tel_segment',
                'label' => __('GSM coverage path segment'),
            )),

            "rid_perc" => array_replace($baseSingleSelectField,array(
                'foreign_key' => 'rid_perc_segment',
                'label' => __('Reduction walkable path segment'),
            )),

            /* Fields current can be update
             * =============================
             */

            "percorr_current" => array_replace($baseSingleSelectField,array(
                'foreign_key' => 'percorr_segment',
                'label' => __('Walkable path segment'),
            )),

            "rid_perc_current" => array_replace($baseSingleSelectField,array(
                'foreign_key' => 'rid_perc_segment',
                'label' => __('Reduction walkable path segment'),
            )),

            "cop_tel_current" => array_replace($baseSingleSelectField,array(
                'foreign_key' => 'cop_tel_segment',
                'label' => __('GSM coverage path segment'),
            )),



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

    protected function _extra_columns_type()
    {
        $fct = array();
        $fct['paths_path_segment'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBTABLE,
                "table_show" => FALSE,
                'url_values' => '/jx/admin/subtablepath?filter=se:$1&path_segment_id=$2',
                'url_values_params' => array(
                    '$1' => 'se',
                    '$2' => 'gid'
                ),
                'datatable' => TRUE,
                'form_show' => array(
                    'insert' => FALSE,
                    'update' => TRUE,
                ),
                'ajax_mode' => self::AJAX_MODE_HTML
            )
        );

        return $fct;
    }


}