<?php defined('SYSPATH') or die('No direct script access.');


trait Controller_Ajax_Base_Cache_GET {

    public function execute()
    {
        // Execute the "before action" method
        $this->before();

        $cache = SAFE::getCache();

        if ($jres = $cache->get($this->request->uri(), FALSE))
        {
            Kohana::$log->add(LOG_DEBUG, 'cache load');
            Kohana::$log->add(LOG_DEBUG, $this->request->uri());
                $this->jres = $jres;
        }
        else
        {
            Kohana::$log->add(LOG_DEBUG, 'cache save');
            Kohana::$log->add(LOG_DEBUG, $this->request->uri());
            // Determine the action to use
            $action = 'action_'.$this->request->action();

            // If the action doesn't exist, it's a 404
            if ( ! method_exists($this, $action))
            {
                throw HTTP_Exception::factory(404,
                    'The requested URL :uri was not found on this server.',
                    array(':uri' => $this->request->uri())
                )->request($this->request);
            }

            // Execute the action itself
            $this->{$action}();

            $cache->set($this->request->uri(),$this->jres,0);
        }




        // Execute the "after action" method
        $this->after();

        // Return the response
        return $this->response;
    }

    public function action_delete() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }

    public function action_update() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }

    public function action_create() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }


}