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
                'diff_current',
                'length',
                'altitude_gap',
                'q_init_current',
                'q_end_current',
                'time_current',
                'rev_time_current',
                'accessibility',
                'inquiry',
                'pdf_print_qrcode',
                'pdf_print_sheet'
            ),
        ),
       array(
            'name' => 'path-foreign-data',
            'position' => 'right',
            'fields' => array(
                'itineraries',
                'path_modes',
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
                'url_path',
                'heights_profile_path'
            ),
        ),
        array(
            'name' => 'path-base-data-path',
            'position' => 'left',
            'fields' => array(
                'nome',
                'loc',
                'se',
                'ex_se',
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
            'name' => 'tab-main',
            'icon' => 'globe',
            'groups' => array(
                'path-data',
                'path-foreign-data',
                'path-base-data-current',
                'path-block-data'
            ),
        ),
        array(
            'name' => 'tab-base-data',
            'icon' => 'mobile-phone',
            'groups' => array(
                'path-base-data-path',
                'path-base-data-geo',
                'path-base-data-data'),
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
                "description" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
                 "accessibility" => array(
                    'form_input_type' => self::TEXTAREA,
                     'editor' => TRUE,
                ),
                "color" => array(
                    "form_input_type" => self::MAPBOX_COLOR,
                    "class" => "color-path",
                ),
                 "width" => array(
                    "default_value" => 3,
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
                "information_url" => array(
                    'prefix' => 'http://'
                ),
                 "altitude_gap" => array(
                    'suffix' => 'm',
                ),
                "length" => array(
                    'suffix' => 'km',
                ),
                /*
                "typology_id" => array(
                    'form_input_type' => self::SELECT,
                    'foreign_mode' => self::SINGLESELECT,
                    'foreign_toshow' => '$1',
                    'foreign_toshow_params' => array(
                        '$1' => 'name',
                    ),
                    'url_values' => '/jx/typology',
                    'label' => __('Main typology'),
                     'description' => __('Select the main typology  for this point of interest'),
                     "table_show" => FALSE,
                ),
                */
                "nome" => array(
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),
                "loc" => array(
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),
                "se" => array(
                    'editable' => FALSE,
                ),
                "ex_se" => array(
                    'editable' => FALSE,
                ),
                "bike" => array(
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),
                "ip" => array(
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),
                "cod_f1" => array(
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),
                "cod_f2" => array(
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),
                "descriz" => array(
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),
                "percorr" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'percorr_segment',
                    'label' => __('Walkable path segment'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                )),

                "rid_perc" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'rid_perc_segment',
                    'label' => __('Reduction walkable path segment'),
                    'editable' => FALSE,
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
                    'editable' => FALSE,
                    'table_show' => FALSE,
                )),

                "em_natur" => array(
                    'label' => __('Natural emergency'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),

                "ev_stcul" => array(
                    'label' => __('History cultural evidences'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),

                "em_paes" => array(
                    'label' => __('Landascape values'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),

                "op_attr" => array(
                    'label' => __('Works and equipment on the path'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),

                "coordxini" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('Start X coordinate'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),

                "coordxen" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('End X coordinate'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),

                "coordyini" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('Start Y coordinate'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),

                "coordyen" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('End Y coordinate'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),

                "q_init" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('Start altitude'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),
                "q_end" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('End altitude'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),
                "diff_q" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('Altitude gap'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),
                "l" => array(
                    'editable' => FALSE,
                    'suffix' => 'km',
                    'label' => __('Length'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),
                "time" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('Travel time'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),
                "rev_time" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('Back travel time'),
                    'editable' => FALSE,
                    'table_show' => FALSE,
                ),

                /* Fields current can be update
                 * =============================
                 */


                "loc_current" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                    'label' => __('Cross places'),
                    'table_show' => FALSE,
                ),

                "em_natur_current" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                    'label' => __('Natural emergency'),
                    'table_show' => FALSE,
                ),

                "em_paes_current" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                    'label' => __('Landascape values'),
                    'table_show' => FALSE,
                ),

                "ev_stcul_current" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                    'label' => __('History cultural evidences'),
                    'table_show' => FALSE,
                ),

                "diff_current" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'diff_segment',
                    'foreign_toshow' => '$1 - $2',
                    'foreign_toshow_params' => array(
                        '$1' => 'code',
                        '$2' => 'description',
                    ),
                    'label' => __('Difficulty typology path segment'),
                    'table_show' => FALSE,
                )),

                "time_current" => array(
                    'suffix' => 'min',
                    'label' => __('Travel time'),
                    'table_show' => FALSE,
                ),
                "rev_time_current" => array(
                    'suffix' => 'min',
                    'label' => __('Back travel time'),
                    'table_show' => FALSE,
                ),
                "q_init_current" => array(
                    'suffix' => 'm',
                    'label' => __('Start quota'),
                    'table_show' => FALSE,
                ),

                "q_end_current" => array(
                    'suffix' => 'm',
                    'label' => __('End quota'),
                    'table_show' => FALSE,
                ),

                "percorr_current" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'percorr_segment',
                    'label' => __('Walkable path segment'),
                    'table_show' => FALSE,
                )),

                "rid_perc_current" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'rid_perc_segment',
                    'label' => __('Reduction walkable path segment'),
                    'table_show' => FALSE,
                )),


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

      /*
        $fct['typologies']  = array_replace($this->_columnStruct,array(
            'data_type' => 'integer',
            'form_input_type' => self::SELECT,
            'foreign_mode' => self::MULTISELECT,
            'foreign_toshow' => '$1',
            'foreign_toshow_params' => array(
                '$1' => 'name',
            ),
            'url_values' => '/jx/typology',
            'label' => __('Typologies'),
             'description' => __('Select one or more typology  for this point of interest'),
             "table_show" => FALSE,
        ));

      */
        
        $fct['path_modes']  = array_replace($this->_columnStruct,array(
            'data_type' => 'integer',
            'form_input_type' => self::SELECT,
            'foreign_mode' => self::MULTISELECT,
            'foreign_key' => 'path_modes',
            'foreign_toshow' => '$1',
            'foreign_toshow_params' => array(
                '$1' => 'mode',
            ),
            'label' => __('Modes'),
             'description' => __('Select one or more modes for  this path'),
             "table_show" => FALSE,
        ));
        
        $fct['url_path'] = array_replace($this->_columnStruct,array(
             
            'data_type' => 'multifield',
            'label' => __('Urls path'),
            "table_show" => FALSE,
            
        ));

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
