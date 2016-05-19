<?php defined('SYSPATH') or die('No direct script access.');


trait Controller_Ajax_Highliting2frontend
{

    protected function _default_filter_list($orm)
    {
        // get section_highliting_typlogolies
        $higliting_typologies = ORM::factory('Highliting_Typology')->find_all();
        $htts = array();
        foreach ($higliting_typologies as $higliting_typology) {
            if ($higliting_typology->has('sections', ORM::factory('Section', array('section' => 'FRONTEND'))))
                $htts[] = $higliting_typology->id;
        }
    
        // get highliting state to show
        $higliting_states_id = [
            2, // ACCETTATA
            4, // ASSEGNATA SUPERVISOR
            7, // NOTIFICATA
            6, // IN ESECUZIONE
            9, // SOSPESA
        ];
    
    
        // only typology to show on frontend
        $orm->where($orm->object_name() .'.publish', 'is', DB::expr('TRUE'))
            ->join('highliting_typologies', 'LEFT')
            ->on($orm->object_name() . '.highliting_typology_id', '=', 'highliting_typologies.id')
            ->where('highliting_typology_id', 'IN', DB::expr('(' . implode(',', $htts) . ')'))
            ->join('highliting_states', 'LEFT')
            ->on($orm->object_name() . '.highliting_state_id', '=', 'highliting_states.id')
            ->where('highliting_state_id', 'IN', DB::expr('(' . implode(',', $higliting_states_id) . ')'));
    }
}
