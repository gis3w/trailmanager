<?php defined('SYSPATH') or die('No direct script access.');

class Task_Role_Add extends Minion_Task
{
    protected $_options = array(
        'capability' => NULL,
        'rolename' => NULL,

    );

    protected $_base_action_capabilities = [
        'insert',
        'update',
        'delete',
        'list',
        'get'
    ];

    /**
     * This is a demo task
     *
     * @return null
     */
    protected function _execute(array $params)
    {
        # before check if capabilities exists
        foreach($this->_base_action_capabilities as $action)
        {
            $capabilityname = $this->_options['capability'].'-'.$action;
            $capabilityORM = ORM::factory('Capability')
                ->where('name','=',$capabilityname)
                ->find();

            if(!isset($capabilityORM->id))
            {
                $capabilityORM->name=$capabilityname;
                $capabilityORM->save();
            }

            #give capability to role
            $role = ORM::factory('Role')
                ->where('name','=',strtoupper($this->_options['rolename']))
                ->find();

            if(isset($role->id))
                $role->add('capabilities',$capabilityORM);

            $capabilityORM->reset();

        }


    }
}