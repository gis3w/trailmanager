<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Admin_Base_Highliting extends Controller_Ajax_Admin_Sheet_Base{

    protected  $_subformToSave = array(
        'image_highliting_poi' => 'Image_Highliting_Poi',
        'image_highliting_path' => 'Image_Highliting_Path',
        'image_highliting_area' => 'Image_Highliting_Area'
    );

    protected function _single_request_row($orm) {
        $toRes = Controller_Ajax_Base_Crud::_single_request_row($orm);

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
     * Method to set reporter
     */
    protected function _insert_reporter()
    {
        $capability = 'admin-'.strtolower($this->_datastructName).'-insert';
        if(isset($this->user)
            AND $this->user->main_role_id != ROLE_REPORTER
            AND $this->user->allow_capa($capability)
            AND !isset($this->_orm->highliting_user_id)
            AND !isset($this->_orm->anonimous_data)
        )
            $this->_orm->highliting_user_id = $this->user->id;

    }

    protected function _data_edit()
    {
        Filter::emptyPostDataToNULL();

        $this->from_state = $this->_orm->highliting_state_id;

        $this->_insert_reporter();

        $this->_set_the_geom_edit();

        $this->_orm->values($_POST);
        if(!isset($this->_orm->id))
        {
            $this->_orm->data_ins = $this->_orm->data_mod = time();
        }
        else
        {
            $this->_orm->data_mod = time();
        }
        $this->_orm->data_mod = time();
        $this->_orm->save();

        $this->_save_subforms_1XN();

        $this->_save_state_passage();

        #$this->_send_email();


    }

    /**
     * To save passages state and note if present
     */
    protected function _save_state_passage()
    {
        $data = array(
            $this->_url_multifield_foreignkey => $this->_orm->id,
            'user_id' => $this->user->id,
            'from_state_id' => $this->from_state,
            'to_state_id' => $_POST['highliting_state_id'],
            'date' =>time(),
        );

        if(isset($_POST['note']) AND $_POST['note'] != '')
            $data['note'] = $_POST['note'];

        $this->_orm->states->values($data)->save();
    }

    /**
     * Set the old notes to show inside sheet
     * @param type $tores
     * @param type $orm
     */
    protected function _set_oldnotes(&$toRes, $orm)
    {
        $view = View::factory('data/oldnotes');
        $view->states = $orm->states
            ->order_by('date','DESC')
            ->find_all();
        $toRes['oldnotes'] = $view->render();
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