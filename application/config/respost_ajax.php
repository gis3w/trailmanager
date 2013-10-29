<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Qui di inseriscono i vari parametri di configurazione delle varie risposta
 */

return array(

    'method' => array(
        'get_item' => array(
            // per i sigoli model
            'user' => array(
                'not_to_show' => array('password','user_data','data_ins','data_mod'),
            ),
            'user_data' => array(
                'not_to_show' => array('id','user_id'),
            ),
            
        ),
        'get_list' => array(
            // per i sigoli model
            'user' => array(
                'not_to_show' => array('password','user_data','data_ins','data_mod'),
            ),
            'user_data' => array(
                'not_to_show' => array('id','user_id'),
            ),
           
        ),
    ),
	
);
