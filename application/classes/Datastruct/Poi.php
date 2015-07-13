<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Poi extends Datastruct {
    
    protected $_nameORM = "Poi";
    protected $_typeORM = "ORMGIS";


    public $icon = 'suitcase';
    public $filter = FALSE;

    public $groups = array(
        array(
            'name' => 'poi-data',
            'position' => 'left',
            'fields' => array('id','publish','title','description','reason','period_schedule','accessibility','inquiry','pdf_print_qrcode'),
        ),
       array(
            'name' => 'poi-foreign-data',
            'position' => 'right',
            'fields' => array('typology_id','typologies','the_geom','max_scale','image_poi','video_poi'),
        ),
        array(
            'name' => 'poi-block-data',
            'position' => 'block',
            'fields' => array('url_poi'),
        ),
        array(
            'name' => 'poi-base-data-poi',
            'position' => 'left',
            'fields' => array(
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
            'name' => 'path-base-data-current',
            'position' => 'right',
            'fields' => array(
                'stato_segn_current',
                'fatt_degr_current',
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
                'note'
            ),
        ),
        array(
            'name' => 'poi-base-data-data',
            'position' => 'block',
            'fields' => array(
                'pt_inter',
                'strut_ric',
                'aree_attr',
                'insediam',
                'pt_acqua',
                'tipo_segna',
                'stato_segn',
                'fatt_degr',
                'pt_socc',
                'coin_in_fi',
                'prio_int',
                'nuov_segna'
            ),
        ),
        array(
            'name' => 'poi-base-data-paths',
            'position' => 'block',
            'fields' => array(
                'paths_poi',
            ),
        ),
    );



    public $tabs = array(
        array(
            'name' => 'tab-main',
            'icon' => 'globe',
            'groups' => array('poi-data','poi-foreign-data','poi-block-data'),
        ),
        array(
            'name' => 'tab-base-data',
            'icon' => 'mobile-phone',
            'groups' => array('poi-base-data-poi','poi-base-data-geo','poi-base-data-survey','path-base-data-current','poi-base-data-data'),
        ),
        array(
            'name' => 'tab-path',
            'icon' => 'location-arrow',
            'groups' => array('poi-base-data-paths'),
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
                "description" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
                 "reason" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
                 "accessibility" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
                "period_schedule" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
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
                ),
                "typology_id" => array(
                    'form_input_type' => self::SELECT,
                    'foreign_mode' => self::SINGLESELECT,
                    'foreign_toshow' => '$1',
                    'foreign_toshow_params' => array(
                        '$1' => 'name',
                    ),
                    'url_values' => '/jx/typology',
                     'description' => __('Select the main typology  for this point of interest'),
                     "table_show" => TRUE,
                ),
                "inquiry" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                ),
                "max_scale" => array(
                    'prefix' => '1:',
                    'table_show' => FALSE,
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

                "pt_inter" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'pt_inter_poi',
                    'label' => __('Point of interest class'),
                )),

                "strut_ric" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'strut_ric_poi',
                    'label' => __('Accomodation building class'),
                )),

                "aree_attr" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'aree_attr_poi',
                    'label' => __('Equip area class'),
                )),

                "insediam" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'insediam_poi',
                    'label' => __('Village class'),
                )),

                "pt_acqua" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'pt_acqua_poi',
                    'label' => __('Water point class'),
                )),

                "tipo_segna" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'tipo_segna_poi',
                    'label' => __('Signage type class'),
                )),

                "stato_segn" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'stato_segn_poi',
                    'label' => __('Signage state class'),
                )),

                "fatt_degr" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'fatt_degr_poi',
                    'label' => __('Degeneration cause class'),
                )),

                "pt_socc" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'pt_socc_poi',
                    'label' => __('Rescue point class'),
                )),

                "coin_in_fi" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'coin_in_fi_poi',
                    'label' => __('Start-end coincidence class'),
                )),

                "prio_int" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'prio_int_poi',
                    'label' => __('Priority intervention class'),
                )),

                "nuov_segna" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'nuov_segna_poi',
                    'label' => __('New signage'),
                )),

                "quota" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('Altitude'),
                ),
                "coord_x" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('X coordinate'),
                ),
                "coord_y" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('Y coordinate'),
                ),

                /* Fields current can be update
               * =============================
               */

                "stato_segn_current" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'stato_segn_poi',
                    'label' => __('Current signage state class'),
                )),

                "fatt_degr_current" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'fatt_degr_poi',
                    'label' => __('Current degeneration cause class'),
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
        ));
        
        $fct['url_poi'] = array_replace($this->_columnStruct,array(
             
            'data_type' => 'multifield',
            'label' => __('Urls poi'),
            "table_show" => FALSE,
            
        ));
        
      
        return $fct;
        
    }
     
    
}
