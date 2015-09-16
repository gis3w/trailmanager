<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Highlitingpoi extends Controller_Ajax_Admin_Base_Highliting{

    protected $_exeLogin = FALSE;

    protected $_pagination = FALSE;

    protected $_datastruct = "Front_Highlitingpoi";

    protected $_url_multifield_foreignkey = 'highliting_poi_id';

    protected $_inheritDatastructName = 'highliting_poi';

    protected  $_multiFilesToSave = array(
        'front_image_highliting_poi' => 'Image_Highliting_Poi',
    );

    public function action_update() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }

    public function action_delete() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }

    public function action_index() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }

    public function before() {
        parent::before();
        if(Auth::instance()->logged_in())
            $this->user = Auth::instance ()->get_user ();
        // we set highliting state
        $_POST['highliting_state_id'] = HSTATE_IN_ACCETTAZIONE;
        // erase front from datastructname
        #$this->_inheritDatastructName = substr($this->_datastructName, 6);
    }

    protected function _data_edit()
    {
        Filter::emptyPostDataToNULL();

        $this->from_state = $this->_orm->highliting_state_id;

        $this->_set_the_geom_edit();

        if(isset($this->user))
            $this->_orm->highliting_user_id = $this->user->id;

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

        // for annonimous segnalation
        if(!isset($this->user))
        {
            //TODO: find a better solution for highliting_path_column
            unset($_POST['highliting_path_id']);
            $this->_orm->anonimous_data->values($_POST);
            $fk = strtolower($this->_inheritDatastructName).'_id';
            $this->_orm->anonimous_data->$fk = $this->_orm->id;
            $this->_orm->anonimous_data->save();
        }


        $this->_save_files_1XN();

        // WE SEND EMAIL FOR CONFIRM E ALERT TO PROTOCOL USER
        $mail = new Email_Newhighliting($this->_orm);
        $mail->send();
    }



}