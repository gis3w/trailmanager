<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Highliting extends ORMGIS {
    
    public $epsg_db = 4326;
    public $epsg_out = 4326;
    
    
  
    protected $_belongs_to = array(
        'highliting_user' => array(
            'model'   => 'User',
            'foreign_key' => 'highliting_user_id'
        ),
        'protocol_user' => array(
            'model'   => 'User',
            'foreign_key' => 'highliting_protocol_id'
        ),
        'supervisor_user' => array(
            'model'   => 'User',
            'foreign_key' => 'supervisor_user_id'
        ),
        'executor_user' => array(
            'model'   => 'User',
            'foreign_key' => 'executor_user_id'
        ),
        'state' => array(
            'model'   => 'Highliting_State',
            'foreign_key' => 'highliting_state_id',
        ),
        'typology' => array(
            'model'   => 'Highliting_Typology',
            'foreign_key' => 'highliting_typology_id'
        ),
        // only for POI
        'highliting_path' => array(
            'model'   => 'Path',
            'foreign_key' => 'highliting_path_id',
        ),
        'pt_inter_code' => array(
            'model'   => 'Pt_Inter_Poi',
            'foreign_key' => 'pt_inter',
            'far_key' => 'code'
        ),
        'strut_ric_code' => array(
            'model'   => 'Strut_Ric_Poi',
            'foreign_key' => 'strut_ric',
            'far_key' => 'code'
        ),
        'aree_attr_code' => array(
            'model'   => 'Aree_Attr_Poi',
            'foreign_key' => 'aree_attr',
            'far_key' => 'code'
        ),
        'insediam_code' => array(
            'model'   => 'Insediam_Poi',
            'foreign_key' => 'insediam',
            'far_key' => 'code'
        ),
        'pt_acqua_code' => array(
            'model'   => 'Pt_Acqua_Poi',
            'foreign_key' => 'pt_acqua',
            'far_key' => 'code'
        ),
        'pt_socc_code' => array(
            'model'   => 'Pt_Socc_Poi',
            'foreign_key' => 'pt_socc',
            'far_key' => 'code'
        ),
    );

    
    protected $_has_one = array(
        'anonimous_data' => array(
            'model'   => 'Anonimous_Highlitings_Data',

        ),
    );
    
    public function labels() {
        return array(
            "subject" => __("Subject"),
            "description" => __("Description"),
            "publish" => __("Published"),
            "typology_id" => __("Main typology"),
            "highliting_state_id" => __('Highliting state to'),
            "supervisor_user_id" => __('Supervisor user'),
            "executor_user_id" => __('Executor user'),
            "data_ins" => __('Creation date'),
            "data_mod" => __('Last update'),
            "the_geom" => __('Geodata'),
            "ending" => __('Ending'),
        );
    }
    
    
    public function rules()
    {
        return array(
            'subject' => array(
                    array('not_empty'),
            ),
            /*
            'highliting_state_id' =>array(
                    array('not_empty'),
            ),
            */
            'highliting_typology_id' =>array(
                    array('not_empty'),
            ),
            'the_geom' =>array(
                    array('not_empty'),
            ),
        );
    }
    
   public function getHighlitingType()
   {
       return $this->_highliting_type;
   }
   
   public function pictures_number()
   {
       return count($this->images->find_all());
   }
   
   public function getNoteByToState($state = NULL)
   {
       if(!isset($state))
           $state = $this->highliting_state_id;
       return $this->states
               ->where('to_state_id','=',(string)$state)
               ->find();
   }


   public function email_abstract($mode = NULL)
   {
       $abstract = View::factory('email/highliting/abstract');
       $abstract->highliting = $this;
       if($mode != 'for_reporter')
       {
        if(isset($this->highliting_user_id))
        {
            $viewReporter = View::factory('data/reporter');
            $viewReporter->user = $this->highliting_user;
        }
         else
         {
             $viewReporter = View::factory('data/anonimous');
             $viewReporter->anonimous = $this->anonimous_data;
         }
       $abstract->reporter = $viewReporter;
       }
        
       
       // type
       switch($this->geotype)
       {
           case(self::TP_POINT):
                $abstract->highliting_type = __('Punctual');
            break;
        
            case(self::TP_MULTILINESTRING):
                $abstract->highliting_type = __('Linear');
            break;
        
            case(self::TP_MULTIPOLYGON):
                $abstract->highliting_type = __('Area');
            break;
       }
               
       return $abstract;
   }

}