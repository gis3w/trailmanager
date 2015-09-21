<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Highliting_Poi extends Datastruct {
    
    protected $_nameORM = "Highliting_Poi";
    protected $_typeORM = "ORMGIS";

    public $formLyoutType = 'form-vertical';
    public $form_table_name = 'Highliting poins';
    public $form_title = 'Highliting point';
    public $icon = 'suitcase';
    public $filter = FALSE;

    public $groups = array(
         array(
            'name' => 'highliting-poi-abstract',
            'position' => 'left',
            'fields' => array('reporter','supervisor','executor','current_highliting_sate','highliting_state_id','supervisor_user_id','executor_user_id'),
             'class' => 'well',
        ),
        array(
            'name' => 'highliting-poi-data',
            'position' => 'left',
            'fields' => array('id','publish','subject','description'),
        ),
       array(
            'name' => 'highliting-poi-foreign-data',
            'position' => 'right',
            'fields' => array(
                'highliting_typology_id',
                'pt_inter',
                'strut_ric',
                'aree_attr',
                'insediam',
                'pt_acqua',
                'pt_socc',
                'percorr',
                'fatt_degr',
                'stato_segn',
                'tipo_segna',
                'highliting_path_id',
                'the_geom',
                'image_highliting_poi'),
        ),
        array(
            'name' => 'highliting-poi-ending',
            'position' => 'right',
            'fields' => array('ending'),
        ),
        array(
            'name' => 'highliting-poi-note',
            'position' => 'block',
            'fields' => array('note','oldnotes'),
        ),
    );
    
    public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "subject"
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

         $baseHighlitingSlave = array_replace($baseSingleSelectField,[
             "table_show" => FALSE,
             "slave_off" => "highliting_typology_id"
         ]);

        
            return array(
                "description" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                    'table_show' => FALSE,
                ),
                 "ending" => array(
                    'form_input_type' => self::TEXTAREA,
                    'editor' => TRUE,
                    'table_show' => FALSE,
                ),
                 "highliting_user_id" => array(
                    'table_show' => FALSE,
                ),
                "protocol_user_id" => array(
                    'table_show' => FALSE,
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

                "highliting_typology_id" => array(
                    'form_input_type' => self::SELECT,
                    'foreign_mode' => self::SINGLESELECT,
                    'foreign_toshow' => '$1',
                    'foreign_toshow_params' => array(
                        '$1' => 'name',
                    ),
                    'label' => __('Highliting typology'),
                    'url_values' => Kohana::$base_url.'jx/admin/highlitingtypology',
                     'description' => __('Select the main typology for this highlighting'),
                     "table_show" => TRUE,
                    "change" => SAFE::setBaseUrl('jx/admin/changehighlitingtypology/'),
                ),

                "highliting_state_id" => array(
                    'data_type' => 'integer',
                    'form_input_type' => self::SELECT,
                    'foreign_mode' => self::SINGLESELECT,
                    'foreign_toshow' => '$1',
                    'foreign_toshow_params' => array(
                        '$1' => 'name',
                    ),
                    'url_values' => SAFE::setBaseUrl('jx/highlitingstate?highliting_state_id=$1'),
                    'url_values_params' => array(
                        '$1' => array(
                            'level' => 'highliting_poi',
                            'field' => 'highliting_state_id',
                        ),    
                        
                   ),
                    "change" => SAFE::setBaseUrl('jx/admin/changehighliting?highliting_state_id='),
                     'description' => __('Select the state of the highliting'),
                     "table_show" => FALSE,
                ),
                "supervisor_user_id" => array(
                    'data_type' => 'integer',
                    'form_input_type' => self::SELECT,
                    'foreign_mode' => self::SINGLESELECT,
                    'foreign_toshow' => '$1 $2',
                    'foreign_toshow_params' => array(
                        '$1' => 'nome',
                        '$2' => 'cognome'
                    ),
                    'url_values' => SAFE::setBaseUrl('jx/admin/user'),
                     'description' => __('Select the supervisor'),
                     "table_show" => FALSE,
                    'editable' => FALSE,
                ),
                "executor_user_id" => array(
                    'data_type' => 'integer',
                    'form_input_type' => self::SELECT,
                    'foreign_mode' => self::SINGLESELECT,
                    'foreign_toshow' => '$1 $2',
                    'foreign_toshow_params' => array(
                        '$1' => 'nome',
                        '$2' => 'cognome'
                    ),
                    'url_values' => SAFE::setBaseUrl('jx/admin/user'),
                     'description' => __('Select the executor'),
                     "table_show" => FALSE,
                    'editable' => FALSE,
                ),
                // we add a note filed for insert only
                'highliting_path_id' => array(
                        'data_type' => 'integer',
                        'description' => __('Select the path'),
                        'label' => _('Path'),
                        'form_input_type' => self::SELECT,
                        'foreign_mode' => self::SINGLESELECT,
                        'foreign_toshow' => '$1',
                        'foreign_toshow_params' => array(
                            '$1' => 'title',
                        ),
                        'url_values' => SAFE::setBaseUrl('jx/pathsclose/$1/$2'),
                        'slave_of' => 'the_geom',
                        'url_values_params' => array(
                            '$1' => 'lon',
                            '$2' => 'lat'
                        ),
                        "table_show" => TRUE,
                        'editable' => TRUE,
                    ),
                // fileds for highliting typology
                'pt_inter' => array_replace($baseHighlitingSlave,array(
                    'foreign_key' => 'pt_inter_poi',
                    'label' => __('Point of interest class'),
                )),
                'strut_ric' => array_replace($baseHighlitingSlave,array(
                    'foreign_key' => 'strut_ric_poi',
                    'label' => __('Accomodation building class'),
                )),
                'aree_attr' => array_replace($baseHighlitingSlave,array(
                    'foreign_key' => 'aree_attr_poi',
                    'label' => __('Equip area class'),
                )),
                'insediam' => array_replace($baseHighlitingSlave,array(
                    'foreign_key' => 'insediam_poi',
                    'label' => __('Village class'),
                )),
                'pt_acqua' => array_replace($baseHighlitingSlave,array(
                    'foreign_key' => 'pt_acqua_poi',
                    'label' => __('Water point class'),
                )),
                'pt_socc' => array_replace($baseHighlitingSlave,array(
                    'foreign_key' => 'pt_socc_poi',
                    'label' => __('Rescue point class'),
                )),
                'percorr' => array_replace($baseHighlitingSlave,array(
                    'foreign_key' => 'percorr_segment',
                    'label' => __('Walkable path segment'),
                )),
                'fatt_degr' => array_replace($baseHighlitingSlave,array(
                    'foreign_key' => 'fatt_degr_poi',
                    'label' => __('Degeneration cause class'),
                )),
                'stato_segn' => array_replace($baseHighlitingSlave,array(
                    'foreign_key' => 'stato_segn_poi',
                    'label' => __('Signage state class'),
                )),
                'tipo_segna' => array_replace($baseHighlitingSlave,array(
                    'foreign_key' => 'tipo_segna_poi',
                    'label' => __('Signage type class'),
                )),


            );
      }
      
     protected function _extra_columns_type()
    {
        $fct = array();
        
        // set the reporter to show in admin section
        $fct['reporter'] = array_replace($this->_columnStruct, array(
                "form_input_type" => self::HTMLTEXT,
                'label' => __('Reporter'),
             )
        );
        
        $fct['supervisor'] = array_replace($this->_columnStruct, array(
                "form_input_type" => self::HTMLTEXT,
                'label' => __('Supervisor'),
             )
        );
        
        $fct['executor'] = array_replace($this->_columnStruct, array(
                "form_input_type" => self::HTMLTEXT,
                'label' => __('Executor'),
             )
        );
        
        
        $fct['oldnotes'] = array_replace($this->_columnStruct, array(
                "form_input_type" => self::HTMLTEXT,
                'label' => __('Notes history'),
                'table_show' => FALSE,
             )
        );
        
        $fct['current_highliting_sate'] = array_replace($this->_columnStruct, array(
                "form_input_type" => self::HTMLTEXT,
                'label' => __('Current highliting state'),
             )
        );
         
         $fct['image_highliting_poi'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBFORM,
                "table_show" => FALSE,
                'foreign_mode' => self::MULTISELECT,
                'foreign_key' => 'highliting_poi_id',
                'validation_url' => 'jx/admin/imagehighlitingpoi',
                'label' => __('Images to upload'),
             )
        );







         // we add a note filed for insert only
         $fct['note'] = array_replace($this->_columnStruct, array(
                "form_input_type" => self::TEXTAREA,
                "editor" => TRUE,
                "table_show" => FALSE,
                'label' => __('Note'),
             )
        );


         
        
        return $fct;
        
    }    
}
