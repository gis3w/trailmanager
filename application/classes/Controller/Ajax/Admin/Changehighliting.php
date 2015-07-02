<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Changehighliting extends Controller_Ajax_Auth_Strict{

    protected $_pagination = FALSE;

    public function action_create() {
        
    }
    
    public function action_update() {
        
    }
    
    public function action_delete() {
        
    }
    
    
    protected function _get_list()
    {
        if(!isset($_GET['highliting_state_id']) OR !in_array($_GET['highliting_state_id'],array(HSTATE_ASSEGNATA_SUPERVISOR,HSTATE_PROGRAMMATA,HSTATE_IN_ESECUZIONE)) OR $this->user->is_a('EXECUTOR'))
            return;
        
        switch($_GET['highliting_state_id'])
        {
            case HSTATE_ASSEGNATA_SUPERVISOR:
            case HSTATE_PROGRAMMATA:
                $role_to_filter = ROLE_SUPERVISOR;
                $field = 'supervisor_user_id';
            break;
        
            case HSTATE_IN_ESECUZIONE:
                $role_to_filter = ROLE_EXECUTOR;
                $field = 'executor_user_id';
            break;
        }
        
        
         // si recuperano i tipi di consegna possibili
        $users = ORM::factory('Role')
                ->where('id','=',$role_to_filter)
                ->find()
                ->users
                ->find_all();
       
     
         $toRes['value']['items'] = array();
         foreach($users as $user)
             $toRes['value']['items'][] = array(
                 'id' => $user->id,
                 'nome' => $user->user_data->nome,
                 'cognome' => $user->user_data->cognome,
                 'offices' => implode(',',  array_keys ($user->offices->find_all()->as_array('name'))),
             );
             
        $toRes['disabled'] = array('value' => FALSE);
        $toRes['value']['default_value'] = array();
        $toRes['value']['value_field'] = 'id';
        $toRes['value']['label_toshow'] = '$1 $2 ($3)';
        $toRes['value']['label_toshow_params'] = array('$1'=>'nome','$2' => 'cognome','$3'=>'offices');

        unset($this->jres->data->items);
        $this->jres->data = array(
            $field => $toRes,
        );
    }
  
}