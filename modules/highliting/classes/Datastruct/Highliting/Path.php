<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Highliting_Path extends Datastruct {
    
    protected $_nameORM = "Highliting_Path";
    protected $_typeORM = "ORMGIS";

    public $formLyoutType = 'form-vertical';
    public $form_table_name = 'Highliting paths';
    public $form_title = 'Highliting path';
    
    public $icon = 'suitcase';
    public $filter = FALSE;

    public $groups = array(
         array(
            'name' => 'highliting-path-abstract',
            'position' => 'left',
            'fields' => array('reporter','supervisor','executor','current_highliting_sate','highliting_state_id','supervisor_user_id','executor_user_id'),
             'class' => 'well',
        ),
        array(
            'name' => 'highliting-path-data',
            'position' => 'left',
            'fields' => array('id','publish','subject','description'),
        ),
       array(
            'name' => 'highliting-path-foreign-data',
            'position' => 'right',
            'fields' => array('highliting_typology_id','highliting_path_id','the_geom','image_highliting_path'),
        ),
        array(
            'name' => 'highliting-path-ending',
            'position' => 'right',
            'fields' => array('ending'),
        ),
        array(
            'name' => 'highliting-path-note',
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
                        self::GEOTYPE_POLYLINE
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
                            'level' => 'highliting_path',
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
                    )
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
         
         $fct['image_highliting_path'] = array_replace($this->_columnStruct, array(
                "data_type" => self::SUBFORM,
                "table_show" => FALSE,
                'foreign_mode' => self::MULTISELECT,
                'foreign_key' => 'highliting_path_id',
                'validation_url' => 'jx/admin/imagehighlitingpath',
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
