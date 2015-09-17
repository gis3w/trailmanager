<?php defined('SYSPATH') or die('No direct script access.');

class Task_Role_Add extends Minion_Task
{
    protected $_options = [
        'capability' => NULL,
        'rolename' => NULL,
        'actions' => [
            'insert',
            'update',
            'delete',
            'list',
            'get'
        ]

    ];

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
        $paramActions = is_array($this->_options['actions']) ? $this->_options['actions'] : preg_split('/,/',$this->_options['actions']);
        # before check if capabilities exists
        foreach($this->_base_action_capabilities as $action)
        {
            if(!in_array($action,$paramActions))
                continue;
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

            if(isset($role->id) AND !$role->has('capabilities',$capabilityORM))
                $role->add('capabilities',$capabilityORM);

            $capabilityORM->reset();

        }


    }
}