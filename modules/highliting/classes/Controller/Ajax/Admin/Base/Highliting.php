<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Admin_Base_Highliting extends Controller_Ajax_Admin_Sheet_Base{

    protected function _single_request_row($orm) {
        $toRes = parent::_single_request_row($orm);

        $this->_unset_ORMGIS_geofield($toRes);
        $this->_set_the_geom($toRes, $orm);
        $this->_set_reporter($toRes,$orm);
        $this->_set_supervisor($toRes,$orm);
        $this->_set_executor($toRes,$orm);
        $this->_set_current_state($toRes,$orm);
        $this->_set_oldnotes($toRes,$orm);

        return $toRes;
    }

    /**
     * Set data from current reporter if anonimous or registerd
     * @param type $toRes
     * @param type $orm
     */
    protected function _set_reporter(&$toRes,$orm)
    {
        if(isset($orm->id))
        {
            if(isset($orm->highliting_user_id))
            {
                $viewReporter = View::factory('data/reporter');
                $viewReporter->user = $orm->highliting_user;
            }
            else
            {
                $viewReporter = View::factory('data/anonimous');
                $viewReporter->anonimous = $orm->anonimous_data;
            }
            $toRes['reporter'] =  $viewReporter->render();

        }
        else
        {
            $toRes['reporter'] = '';
        }
    }

    /**
     *  Set data from current supervisor
     * @param type $toRes
     * @param type $orm
     */
    protected function _set_supervisor(&$toRes,$orm)
    {
        $viewSupervisor = View::factory('data/supervisor');
        $viewSupervisor->user = $orm->supervisor_user;
        $toRes['supervisor'] =  $viewSupervisor->render();
    }

    /**
     *  Set data from current executor
     * @param type $toRes
     * @param type $orm
     */
    protected function _set_executor(&$toRes,$orm)
    {
        $viewExectuor = View::factory('data/executor');
        $viewExectuor->user = $orm->executor_user;
        $toRes['executor'] =  $viewExectuor->render();
    }

    /**
     * Set the current highliting state ìfor human reading
     * @param type $toRes
     * @param type $orm
     */
    protected function _set_current_state(&$toRes,$orm)
    {
        if(isset($orm->state->name))
        {
            $view = View::factory('data/currentstate');
            $view->state = $orm->state;
            $toRes['current_highliting_sate'] = $view->render();
        }

    }

}