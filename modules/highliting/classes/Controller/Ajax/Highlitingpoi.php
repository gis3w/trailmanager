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

    protected $_image_uri ="/download/imagehighlitingpoi/index/";
    protected $_image_thumb_uri ="/download/imagehighlitingpoi/thumbnail/";

    public function action_update() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }

    public function action_delete() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }
    /*
    public function action_index() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }
    */

    public function before() {
        parent::before();
        if(Auth::instance()->logged_in())
            $this->user = Auth::instance ()->get_user ();
        // we set highliting state
        $_POST['highliting_state_id'] = HSTATE_IN_ACCETTAZIONE;
        // erase front from datastructname
        #$this->_inheritDatastructName = substr($this->_datastructName, 6);
    }


    protected function _default_filter_list($orm)
    {
        // get section_highliting_typlogolies
        $higliting_typologies = ORM::factory('Highliting_Typology')->find_all();
        $htts = array();
        foreach ($higliting_typologies as $higliting_typology)
        {
            if ($higliting_typology->has('sections', ORM::factory('Section', array('section' => 'FRONTEND'))))
                $htts[] = $higliting_typology->id;
        }

        // only typology to show on frontend
        $orm->join('highliting_typologies','LEFT')
            ->on($orm->object_name().'.highliting_typology_id','=','highliting_typologies.id')
            ->where('highliting_typology_id', 'IN', DB::expr('('.implode(',',$htts).')'));
    }
    
    protected function _single_request_row($orm)
    {
        $toRes = Controller_Ajax_Base_Crud::_single_request_row($orm);

        $this->_unset_ORMGIS_geofield($toRes);
        $toRes['geoJSON'] = $orm->asgeojson_php;

        # data to unset:
        foreach ( array(
            'highliting_user_id',
            'protocol_user_id',
            'supervisor_user_id',
            'data_ins',
            'data_mod',
            'ending',
            'executor_user_id',
            'highliting_path_id',
            'strut_ric',
            'pt_inter',
            'aree_attr',
            'insediam',
            'pt_acqua',
            'pt_socc',
            'percorr',
            'fatt_degr',
            'stato_segn',
            'tipo_segna',



            ) as $toUnSet)
        {
            unset($toRes[$toUnSet]);
        }

        $images = $orm->images->find_all();
        $toRes['images'] = array();
        foreach($images as $image)
            $toRes['images'] = array(
                'image_url' => $this->_image_uri.$image->file,
                'image_thumb_url' => $this->_image_thumb_uri.$image->file,
                'description' => $image->description
            );


        return $toRes;
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