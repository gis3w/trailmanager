<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Highlitingsummary extends Controller_Ajax_Auth_Strict{
    
    protected $_base_route = 'jx/admin';
    /**
     * Contiene il nome dell'orm richiesto
     * @var String
     */
    protected $_tb;
    
    public function action_update(){}
    public function action_insert(){}
    public function action_delete(){}

    public function action_index() {
        
        $user_last_login = $this->session->get('user_last_login');
        $current_time = time();
        
       // situation highlitings
       $poi = json_decode(Request::factory(Route::url($this->_base_route, array('controller' => 'highlitingpoi')))
                            ->execute()
                            ->body());
        $path = json_decode(Request::factory(Route::url($this->_base_route, array('controller' => 'highlitingpath')))
                            ->execute()
                            ->body());

        
        //build statistcs
        $traslates = array(
            'poi' => __('Punctuals'),
            'path' =>__('Linears'),
        );
        
        $stats = array(
            __('Punctuals') => array(),
            __('Linears') =>array(),
        );
        
        $typologies = ORM::factory('Highliting_Typology')->find_all()->as_array('id');
        $traslates_typology = array();
        foreach($typologies as $typology_id => $typology)
        {
            $traslates_typology[$typology_id] = View::factory('data/highlitingtypology');
            $traslates_typology[$typology_id]->typology = $typology;
            $traslates_typology[$typology_id] = $traslates_typology[$typology_id]->render();
        }
        
        $states = ORM::factory('Highliting_State')->find_all()->as_array('id');
        $traslates_state = array();
        foreach($states as $state_id => $state)
        {
            $traslates_state[$state_id] = View::factory('data/currentstate');
            $traslates_state[$state_id]->state = $state;
            $traslates_state[$state_id] = $traslates_state[$state_id]->render();
        }

        
        foreach($traslates as $htype => $t)
        {
            foreach(${$htype}->data->items as $h)
            {
                $data_ins = DateTime::createFromFormat('d/m/Y H:i', $h->data_ins);
                
                
                if(!isset($stats[$t][__('Total')]))
                    $stats[$t][__('Total')] = 0;
                if(!isset($stats[$t][__('New')]))
                    $stats[$t][__('New')] = 0;
                 if(!isset($stats[$t][__('Typology')]))
                    $stats[$t][__('Typology')] = array();
                 if(!isset($stats[$t][__('State')]))
                    $stats[$t][__('State')] = array();
                if(!isset($stats[$t][__('Typology')][$traslates_typology[$h->highliting_typology_id]]))
                    $stats[$t][__('Typology')][$traslates_typology[$h->highliting_typology_id]] = 0;
                if(!isset($stats[$t][__('State')][$traslates_state[$h->highliting_state_id]]))
                    $stats[$t][__('State')][$traslates_state[$h->highliting_state_id]] = 0;
                
                
                $stats[$t][__('Typology')][$traslates_typology[$h->highliting_typology_id]] += 1;
                $stats[$t][__('State')][$traslates_state[$h->highliting_state_id]] += 1;
                $stats[$t][__('Total')] += 1;
                if($data_ins instanceof DateTime AND $data_ins->getTimestamp() >= $user_last_login)
                    $stats[$t][__('New')] +=1;
            }
        }
            
        
        $res = array();
        //general
        $res['general'] = array();
        $res['general']['total_highlightings'] = count($poi->data->items) + count($path->data->items);
        $res['general'] += $stats; 
        // total for typology
        $this->jres->data = $res;
        
    }
}