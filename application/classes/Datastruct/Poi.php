<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Poi extends Datastruct {
    
    protected $_nameORM = "Poi";
    protected $_typeORM = "ORMGIS";

    public $form_table_name = 'Pois';
    public $form_title = 'Poi';
    public $formLyoutType = 'form-vertical';
    public $icon = 'suitcase';
    public $filter = FALSE;

    public $groups = array(
        array(
            'name' => 'poi-data',
            'position' => 'left',
            'fields' => array(
                'id',
                'data_ins',
                'data_mod',
                'publish',
                'pdf_print_qrcode',
                'pdf_print_sheet',

            ),
        ),
       array(
            'name' => 'poi-foreign-data',
            'position' => 'right',
            'fields' => array(
                'itineraries',
                'the_geom',
                'max_scale',
                'image_poi',
                'video_poi',
                'path_id'),
        ),
        array(
            'name' => 'poi-base-data-poi',
            'position' => 'left',
            'fields' => array(
                'idwp',
                'id_palo',
                'se',
                'bike',
                'ip',
                'cod_f1',
                'cod_f2',
            ),
        ),
        array(
            'name' => 'poi-base-data-geo',
            'position' => 'right',
            'fields' => array(
                'coord_x',
                'coord_y',
                'quota',
            ),
        ),

        array(
            'name' => 'poi-base-data-survey',
            'position' => 'left',
            'fields' => array(
                'data_ril',
                'condmeteo',
                'rilev',
                'class_ril',
                'photo',
                'note',
                'note_man'

            ),
        ),
        array(
            'name' => 'poi-base-data-data',
            'position' => 'right',
            'fields' => array(
                'pt_inter',
                'strut_ric',
                'aree_attr',
                'insediam',
                'pt_acqua',
                'pt_socc',
                'coin_in_fi',
            ),
        ),
        array(
            'name' => 'poi-base-data-segna',
            'position' => 'right',
            'fields' => array(
                'tipo_segna',
                'stato_segn',
                'nuov_segna',
            ),
        ),
        array(
            'name' => 'poi-base-data-degr',
            'position' => 'right',
            'fields' => array(
                'fatt_degr'
            ),
        ),
        array(
            'name' => 'poi-base-data-paths',
            'position' => 'block',
            'fields' => array(
                'paths_poi',
            ),
        ),
        array(
            'name' => 'poi-base-data-path-segments',
            'position' => 'block',
            'fields' => array(
                'path_segments_poi',
            ),
        ),


    );



    public $tabs = array(
        array(
            'name' => 'tab-base-data',
            'icon' => 'mobile-phone',
            'groups' => array(
                'poi-base-data-poi',
                'poi-base-data-geo',
                'poi-base-data-survey',
                'poi-base-data-data',
                'poi-base-data-segna',
                'poi-base-data-degr'
            ),
        ),
        array(
            'name' => 'tab-main',
            'icon' => 'globe',
            'groups' => array(
                'poi-data',
                'poi-foreign-data',
            ),
        ),
        array(
            'name' => 'tab-path',
            'icon' => 'location-arrow',
            'groups' => array('poi-base-data-paths'),
        ),
        array(
            'name' => 'tab-path-segment',
            'icon' => 'location-arrow',
            'groups' => array('poi-base-data-path-segments'),
        )



    );
    
    public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "idwp"
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
                "path_id" => array(
                    'editable' => FALSE,
                    'table_show' => FALSE,
                    'form_show' => FALSE
                ),
                "data_ins" => array(
                    'editable' => FALSE,
                    'table_show' => TRUE,
                ),
                "data_mod" => array(
                    'editable' => FALSE,
                    'table_show' => TRUE,
                ),
                "the_geom" => array(
                    'form_input_type' => self::MAPBOX,
                    'map_box_editing' => TRUE,
                    'map_box_editing_geotype' => array(
                        self::GEOTYPE_MARKER
                    ),
                    'map_box_fileloading' => TRUE,
                    'label' =>__('Geodata'),
                    'table_show' => FALSE,
                    'overlays' => '/jx/admin/path/$1',
                    'overlays_parmas' => [
                        '$1' => 'path_id',
                    ]
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
                     'description' => __('Select the main typology  for this point of interest'),
                     "table_show" => FALSE,
                     "editable" => FALSE,
                ),
                */
                "max_scale" => array(
                    'prefix' => '1:',
                    'table_show' => FALSE,
                ),
                "bike" => array(
                    "table_show" => FALSE,
                ),
                "ip" => array(
                    "table_show" => FALSE,
                ),
                "cod_f1" => array(
                    "table_show" => FALSE,
                ),
                "cod_f2" => array(
                    "table_show" => FALSE,
                ),
                "photo" => array(
                    'table_show' => FALSE,

                ),
                "note" => array(
                    'table_show' => FALSE,
                    'form_input_type' => self::TEXTAREA,
                ),
                "note_man" => array(
                    'table_show' => FALSE,
                    'form_input_type' => self::TEXTAREA,
                ),
                "quali_ril" => array(
                    "table_show" => FALSE,
                ),

                "class_ril" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'class_ril_segment',
                    'foreign_value_field' => 'class',
                    "table_show" => FALSE,
                )),

                "pt_inter" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'pt_inter_poi',
                )),

                "strut_ric" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'strut_ric_poi',
                )),

                "aree_attr" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'aree_attr_poi',
                )),

                "insediam" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'insediam_poi',
                )),

                "pt_acqua" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'pt_acqua_poi',
                )),

                "tipo_segna" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'tipo_segna_poi',
                )),

                "stato_segn" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'stato_segn_poi',
                )),

                "fatt_degr" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'fatt_degr_poi',
                )),

                "pt_socc" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'pt_socc_poi',
                )),

                "coin_in_fi" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'coin_in_fi_poi',
                )),

                "prio_int" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'prio_int_poi',
                )),

                "nuov_segna" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'nuov_segna_poi',
                )),

                "quota" => array(
                    'suffix' => 'm',
                    'table_show' => FALSE,
                ),
                "coord_x" => array(
                    'suffix' => 'm',
                    "table_show" => FALSE,
                ),
                "coord_y" => array(
                    'suffix' => 'm',
                    "table_show" => FALSE,
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
                    'delete' => 'jx/admin/upload/image?file=$1',
                    'delete_options' => array(
                        '$1' => 'nome',
                    ),
                    'download' => 'admin/download/image/$1/$2',
                    'download_options' => array(
                        '$1' => 'poi_id',
                        '$2' => 'nome',
                        ),
                ),
             )
        );
         
         $fct['image_poi'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBFORM,
                "table_show" => FALSE,
                'foreign_mode' => self::MULTISELECT,
                'foreign_key' => 'poi_id',
                'validation_url' => 'jx/admin/imagepoi',
                'label' => __('Images to upload'),
             )
        );
         
         
        $fct['video_poi'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBFORM,
                "table_show" => FALSE,
                'foreign_mode' => self::MULTISELECT,
                'foreign_key' => 'poi_id',
                'validation_url' => 'jx/admin/videopoi',
                'label' => __('Videos to embed'),
             )
        );
        
        $fct['pdf_print_qrcode']  = array_replace($this->_columnStruct,array(
                    'form_input_type' => self::BUTTON,
                    'input_class' => 'default',
                    'data_type' => 'pdf_print',
                    'url_values' => '/admin/download/qrcode/poi/$1',
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
                'url_values' => '/print/poi/sheet/$1',
                'url_values_params' => array(
                    '$1' => 'id',
                ),
                'description' => __('Print poi sheet'),
                'table_show' => FALSE,
                'label' => __(''),
                'icon' => 'print',
                'form_show' => array(
                    self::STATE_INSERT => FALSE,
                    self::STATE_UPDATE =>TRUE
                ),
            )
        );

        $fct['paths_poi'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBTABLE,
                "table_show" => FALSE,
                'url_values' => '/jx/admin/subtablepath?filter=se:$1&poi_id=$2',
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

        $fct['path_segments_poi'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBTABLE,
                "table_show" => FALSE,
                'url_values' => '/jx/admin/subtablepathsegment?filter=se:$1&poi_id=$2',
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
        
        return $fct;
        
    }


      protected function _foreign_column_type() {
                
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
             "editable" => FALSE,
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
