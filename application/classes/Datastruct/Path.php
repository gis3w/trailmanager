<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Path extends Datastruct {
    
    protected $_nameORM = "Path";
    protected $_typeORM = "ORMGIS";

    public $formLyoutType = 'form-vertical';
    public $form_table_name = 'Paths';
    public $form_title = 'Path';
    public $icon = 'location-arrow';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'path-data',
            'position' => 'left',
            'fields' => array(
                'id',
                'data_ins',
                'data_mod',
                'title',
                'publish',
                'description',
                'pdf_print_qrcode',
                'pdf_print_sheet'
            ),
        ),
       array(
            'name' => 'path-foreign-data',
            'position' => 'right',
            'fields' => array(
                'itineraries',
                'the_geom',
                'color',
                'width',
                'image_path',
                'video_path'
            ),
        ),
        array(
            'name' => 'path-block-data',
            'position' => 'block',
            'fields' => array(
                'heights_profile_path'
            ),
        ),
        array(
            'name' => 'path-base-data-path',
            'position' => 'left',
            'fields' => array(
                'nome',
                'se',
                'ex_se',
                'loc',
                'bike',
                'ip',
                'cod_f1',
                'cod_f2',
            ),
        ),

        array(
            'name' => 'path-base-data-geo',
            'position' => 'right',
            'fields' => array(
                'coordxini',
                'coordyini',
                'coordxen',
                'coordyen',
                'q_init',
                'q_end',
                'diff_q',
                'l'),
        ),

        array(
            'name' => 'path-base-data-current',
            'position' => 'left',
            'fields' => array(
                'loc_current',
                'em_natur_current',
                'em_paes_current',
                'ev_stcul_current',
                'percorr_current',
                'rid_perc_current',
            ),
        ),

        array(
            'name' => 'path-base-data-data',
            'position' => 'block',
            'fields' => array(
                'descriz',
                'percorr',
                'rid_perc',
                'em_natur',
                'em_paes',
                'ev_stcul',
                'op_attr',
                'diff',
                'time',
                'rev_time'),
        ),

        array(
            'name' => 'path-base-data-poi',
            'position' => 'block',
            'fields' => array(
                'pois_path',
            ),
        ),

        array(
            'name' => 'path-base-data-path-segment',
            'position' => 'block',
            'fields' => array(
                'path_segments_path',
            ),
        ),

        array(
            'name' => 'path-highlitingpoi',
            'position' => 'block',
            'fields' => array(
                'highlitingpoi_path',
            ),
        ),

    );

    public $tabs = array(
        array(
            'name' => 'tab-base-data',
            'icon' => 'mobile-phone',
            'groups' => array(
                'path-base-data-path',
                'path-base-data-geo',
                'path-base-data-data'),
        ),
        array(
            'name' => 'tab-main',
            'icon' => 'globe',
            'groups' => array(
                'path-data',
                'path-foreign-data',
                'path-block-data'
            ),
        ),
        array(
            'name' => 'tab-poi',
            'icon' => 'map-marker',
            'groups' => array('path-base-data-poi'),
        ),
        array(
            'name' => 'tab-path-segment',
            'icon' => 'location-arrow',
            'groups' => array('path-base-data-path-segment'),
        ),
        array(
            'name' => 'tab-path-highlitingpoi',
            'icon' => 'map-marker',
            'groups' => array('path-highlitingpoi'),
        )

    );
    
    public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "title"
        )
    );
    
     protected function _columns_type() {

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
                "data_ins" => array(
                    'editable' => FALSE,
                    'table_show' => TRUE,
                    'label' => __('Insert date'),
                ),
                "data_mod" => array(
                    'editable' => FALSE,
                    'table_show' => TRUE,
                    'label' => __('Update date'),
                ),
                "color" => array(
                    "form_input_type" => self::MAPBOX_COLOR,
                    "class" => "color-path",
                    "table_show" => FALSE,
                ),
                 "width" => array(
                    "default_value" => 3,
                    'suffix' => 'px',
                     "table_show" => FALSE,
                ),
                 "the_geom" => array(
                    'form_input_type' => self::MAPBOX,
                    'map_box_editing' => TRUE,
                    'map_box_editing_geotype' => array(
                        self::GEOTYPE_POLYLINE
                    ),
                    'map_box_fileloading' => TRUE,
                    'table_show' => FALSE,
                ),
                "loc" => array(
                    'table_show' => FALSE,
                ),
                "bike" => array(
                    'table_show' => FALSE,
                ),
                "ip" => array(
                    'table_show' => FALSE,
                ),
                "cod_f1" => array(
                    'table_show' => FALSE,
                ),
                "cod_f2" => array(
                    'table_show' => FALSE,
                ),
                "descriz" => array(
                    'table_show' => FALSE,
                    'form_input_type' => self::TEXTAREA,
                ),
                "percorr" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'percorr_segment',
                    'label' => __('Walkable path segment'),
                    'table_show' => FALSE,
                )),

                "rid_perc" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'rid_perc_segment',
                    'label' => __('Reduction walkable path segment'),
                    'table_show' => FALSE,
                )),

                "diff" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'diff_segment',
                    'foreign_toshow' => '$1 - $2',
                    'foreign_toshow_params' => array(
                        '$1' => 'code',
                        '$2' => 'description',
                    ),
                    'label' => __('Difficulty typology path segment'),
                    'table_show' => FALSE,
                )),

                "em_natur" => array(
                    'label' => __('Natural emergency'),
                    'form_input_type' => self::TEXTAREA,
                    'table_show' => FALSE,
                ),

                "ev_stcul" => array(
                    'label' => __('History cultural evidences'),
                    'form_input_type' => self::TEXTAREA,
                    'table_show' => FALSE,
                ),

                "em_paes" => array(
                    'label' => __('Landascape values'),
                    'form_input_type' => self::TEXTAREA,
                    'table_show' => FALSE,
                ),

                "op_attr" => array(
                    'label' => __('Works and equipment on the path'),
                    'form_input_type' => self::TEXTAREA,
                    'table_show' => FALSE,
                ),

                "coordxini" => array(
                    'suffix' => 'm',
                    'label' => __('Start X coordinate'),
                    'table_show' => FALSE,
                ),

                "coordxen" => array(
                    'suffix' => 'm',
                    'label' => __('End X coordinate'),
                    'table_show' => FALSE,
                ),

                "coordyini" => array(
                    'suffix' => 'm',
                    'label' => __('Start Y coordinate'),
                    'table_show' => FALSE,
                ),

                "coordyen" => array(
                    'suffix' => 'm',
                    'label' => __('End Y coordinate'),
                    'table_show' => FALSE,
                ),

                "q_init" => array(
                    'suffix' => 'm',
                    'label' => __('Start altitude'),
                    'table_show' => FALSE,
                ),
                "q_end" => array(
                    'suffix' => 'm',
                    'label' => __('End altitude'),
                    'table_show' => FALSE,
                ),
                "diff_q" => array(
                    'suffix' => 'm',
                    'label' => __('Altitude gap'),
                    'table_show' => FALSE,
                ),
                "l" => array(
                    'suffix' => 'km',
                    'label' => __('Length'),
                    'table_show' => FALSE,
                ),
                "time" => array(
                    'suffix' => 'm',
                    'label' => __('Travel time'),
                    'table_show' => FALSE,
                ),
                "rev_time" => array(
                    'suffix' => 'm',
                    'label' => __('Back travel time'),
                    'table_show' => FALSE,
                ),




            );
      }
      
        protected function _extra_columns_type()
    {
        $fct = array();

         $fct['images'] = array_replace($this->_columnStruct, array(
                "form_input_type" => self::INPUT,
                "multiple" => FALSE,
                "data_type" => 'jquery_fileupload',
                "form_show" => TRUE,
                "table_show" => FALSE,
               "subform_table_show" => TRUE, 
                'label' =>__('Images to upload'),
                'urls' => array(
                    'data' => 'jx/admin/upload/image',
                    'delete' => 'jx//admin/upload/image?file=$1',
                    'delete_options' => array(
                        '$1' => 'nome',
                    ),
                    'download' => 'admin/download/image/$1/$2',
                    'download_options' => array(
                        '$1' => 'path_id',
                        '$2' => 'nome',
                        ),
                ),
             )
        );
         
         $fct['image_path'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBFORM,
                "table_show" => FALSE,
                'foreign_mode' => self::MULTISELECT,
                'foreign_key' => 'path_id',
                'validation_url' => 'jx/admin/imagepath',
                'label' => __('Images to upload'),
             )
        );
         
        $fct['video_path'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBFORM,
                 'form_name' => 'video_path',
                "table_show" => FALSE,
                'foreign_mode' => self::MULTISELECT,
                'foreign_key' => 'path_id',
                'validation_url' => 'jx/admin/videopath',
                'label' => __('Videos to embed'),
             )
        );
        
        $fct['pdf_print_qrcode']  = array_replace($this->_columnStruct,array(
                    'form_input_type' => self::BUTTON,
                    'input_class' => 'default',
                    'data_type' => 'pdf_print',
                    'url_values' => '/admin/download/qrcode/path/$1',
                    'url_values_params' => array(
                        '$1' => 'id',
                    ),
                    'description' => __('Download qrcode position'),
                    'table_show' => FALSE,
                    'label' => __(''),
                    'icon' => 'qrcode',
                    'form_show' => array(
                        self::STATE_INSERT => FALSE,
                        self::STATE_UPDATE =>TRUE
                    ),
                )
        );

        $fct['pdf_print_sheet']  = array_replace($this->_columnStruct,array(
                'form_input_type' => self::BUTTON,
                'input_class' => 'default',
                'data_type' => 'pdf_print',
                'url_values' => '/print/path/sheet/$1',
                'url_values_params' => array(
                    '$1' => 'id',
                ),
                'description' => __('Print path sheet'),
                'table_show' => FALSE,
                'label' => __(''),
                'icon' => 'print',
                'form_show' => array(
                    self::STATE_INSERT => FALSE,
                    self::STATE_UPDATE =>TRUE
                ),
            )
        );

        $fct['pois_path'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBTABLE,
                "table_show" => FALSE,
                'url_values' => '/jx/admin/subtablepoi?filter=se:$1&path_id=$2',
                'url_values_params' => array(
                    '$1' => 'se',
                    '$2' => 'id'
                ),
                'datatable' => TRUE,
                'form_show' => array(
                    'insert' => FALSE,
                    'update' => TRUE,
                ),
                'ajax_mode' => self::AJAX_MODE_HTML
            )
        );

        $fct['path_segments_path'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBTABLE,
                "table_show" => FALSE,
                'url_values' => '/jx/admin/subtablepathsegment?filter=se:$1&path_id=$2',
                'url_values_params' => array(
                    '$1' => 'se',
                    '$2' => 'id'
                ),
                'datatable' => FALSE,
                'form_show' => array(
                    'insert' => FALSE,
                    'update' => TRUE,
                ),
                'ajax_mode' => self::AJAX_MODE_HTML
            )
        );

        $fct['highlitingpoi_path'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBTABLE,
                "table_show" => FALSE,
                'url_values' => '/jx/admin/subtablehighlitingpoi?filter=highliting_path_id:$1',
                'url_values_params' => array(
                    '$1' => 'id'
                ),
                'datatable' => FALSE,
                'form_show' => array(
                    'insert' => FALSE,
                    'update' => TRUE,
                ),
                'ajax_mode' => self::AJAX_MODE_HTML
            )
        );

        $fct['heights_profile_path'] = array_replace($this->_columnStruct, array(
                "form_show" => FALSE,
                "data_type" => self::C3CHART,
                "c3chart_type" => self::C3CHART_TYPE_LINECHART,
                "c3chart_x_axis" => 'cds2d',
                "c3chart_y_axis" => 'z',
                "table_show" => FALSE,
                'url_values' => '/jx/heightsprofilepath/$1',
                'url_values_params' => array(
                    '$1' => 'id',

                ),
                'datatable' => FALSE,
            )
        );
        
        return $fct;
        
    }


      protected function _foreign_column_type() {




          $fct['itineraries']  = array_replace($this->_columnStruct,array(
              'data_type' => 'integer',
              'form_input_type' => self::SELECT,
              'foreign_mode' => self::MULTISELECT,
              'foreign_toshow' => '$1',
              'foreign_toshow_params' => array(
                  '$1' => 'name',
              ),
              'url_values' => '/jx/admin/itinerary',
              'label' => __('Itineraries'),
              'description' => __('Select one or more itineraries'),
              "table_show" => FALSE,
          ));



        
      
        return $fct;
        
    }
 
     
    
}
