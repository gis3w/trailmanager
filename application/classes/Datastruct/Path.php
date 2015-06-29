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
            'name' => 'path-base-data',
            'position' => 'block',
            'fields' => array('se','percorr','rid_perc','em_natura','em_paes','ev_stcul','op_attr'),
        ),
    );

    public $tabs = array(
        array(
            'name' => 'tab-main',
            'groups' => array('path-data','path-foreign-data','path-block-data'),
        ),
        array(
            'name' => 'tab-base-data',
            'groups' => array('path-base-data'),
        ),
    );
    
    public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "title"
        )
    );
    
     protected function _columns_type() {
        
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
                "se" => array(
                    'editable' => FALSE,
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
                "percorr" => array(
                    'form_input_type' => self::SELECT,
                    'foreign_mode' => self::SINGLESELECT,
                    'foreign_value_field' => 'code',
                    'foreign_toshow' => '$1',
                    'foreign_toshow_params' => array(
                        '$1' => 'description',
                    ),
                    'url_values' => '/jx/admin/administration/percorrsegments',
                    'label' => __('Walkable mode'),
                    "table_show" => TRUE,
                ),
                "rid_perc" => array(
                    'form_input_type' => self::SELECT,
                    'foreign_mode' => self::SINGLESELECT,
                    'foreign_value_field' => 'code',
                    'foreign_toshow' => '$1',
                    'foreign_toshow_params' => array(
                        '$1' => 'description',
                    ),
                    'url_values' => '/jx/admin/administration/ridpercsegments',
                    'label' => __('Reduction walkable path'),
                    "table_show" => TRUE,
                ),
                "em_natura" => array(
                    'form_input_type' => self::TEXTAREA,
                    'label' => __('Natural emergency'),
                ),
                "ev_stcul" => array(
                    'form_input_type' => self::TEXTAREA,
                    'label' => __('History cultural evidences'),
                ),
                "em_paes" => array(
                    'form_input_type' => self::TEXTAREA,
                    'label' => __('Landascape values'),
                ),
                "op_attr" => array(
                    'form_input_type' => self::TEXTAREA,
                    'label' => __('Works and equipment on the path'),
                ),
                 "inquiry" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
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
