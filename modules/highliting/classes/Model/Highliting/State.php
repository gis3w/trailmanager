<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Highliting_State extends ORM {

    protected $_belongs_to = array(
        'user' => array(
            'model'   => 'User',
        ),
        'from_state' => array(
            'model'   => 'Highliting_state',
            'foreign_key' => 'from_state_id'
        ),
        'to_state' => array(
            'model'   => 'Highliting_state',
            'foreign_key' => 'to_state_id'
        ),
    );
}