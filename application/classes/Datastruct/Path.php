<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Path extends Datastruct {
    
    protected $_nameORM = "Path";
    protected $_typeORM = "ORMGIS";
    
    public $icon = 'location-arrow';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'path-data',
            'position' => 'left',
            'fields' => array('id','title','publish','description','length','altitude_gap','reason','period_schedule','general_features','accessibility','inquiry','pdf_print_qrcode'),
        ),
       array(
            'name' => 'path-foreign-data',
            'position' => 'right',
            'fields' => array('typology_id','typologies','path_modes','the_geom','color','width','image_path','video_path'),
        ),
        array(
            'name' => 'path-block-data',
            'position' => 'block',
            'fields' => array('url_path'),
        ),
        array(
            'name' => 'path-base-data-path',
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
            'name' => 'path-base-data-geo',
            'position' => 'right',
            'fields' => array(
                'coordxini',
                'coordyini',
                'coordxen',
                'coordyen',
                'q_init',
                'q_end',
                'l'),
        ),

        array(
            'name' => 'path-base-data-current',
            'position' => 'left',
            'fields' => array(
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
                'op_attr'),
        ),

    );

    public $tabs = array(
        array(
            'name' => 'tab-main',
            'groups' => array('path-data','path-foreign-data','path-block-data'),
        ),
        array(
            'name' => 'tab-base-data',
            'groups' => array('path-base-data-path','path-base-data-geo','path-base-data-current','path-base-data-data'),
        ),
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
                "general_features" => array(
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
                 "period_schedule" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
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
                     "table_show" => TRUE,
                ),

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
                "descriz" => array(
                    'editable' => FALSE,
                ),
                "percorr" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'percorr_segment',
                    'label' => __('Walkable path segment'),
                )),

                "rid_perc" => array_replace($baseSingleSelectField,array(
                    'foreign_key' => 'rid_perc_segment',
                    'label' => __('Reduction walkable path segment'),
                )),

                "em_natur" => array(
                    'label' => __('Natural emergency'),
                ),

                "ev_stcul" => array(
                    'label' => __('History cultural evidences'),
                ),

                "em_paes" => array(
                    'label' => __('Landascape values'),
                ),

                "op_attr" => array(
                    'label' => __('Works and equipment on the path'),
                ),

                "coordxini" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('Start X coordinate'),
                ),

                "coordxen" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('End X coordinate'),
                ),

                "coordyini" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('Start Y coordinate'),
                ),

                "coordyen" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('End Y coordinate'),
                ),

                "q_init" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('Start altitude'),
                ),
                "q_end" => array(
                    'editable' => FALSE,
                    'suffix' => 'm',
                    'label' => __('End altitude'),
                ),
                "l" => array(
                    'editable' => FALSE,
                    'suffix' => 'km',
                    'label' => __('Length'),
                ),

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
        
      
        return $fct;
        
    }
 
     
    
}
