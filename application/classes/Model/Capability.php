<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Capability Model

 */
class Model_Capability extends ORM {
	
    protected $_has_many = array
    (
        'roles' => array('model' => 'Role', 'through' => 'capabilities_roles')
    );
        
    public function labels() {
        return array(
            "description" => __("descrizione"),
            "name" => __("nome capability"),
        );
    }
    
    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty'),
                array(array($this, 'unique'), array('name', ':value')),
            ),
        );
    }


}