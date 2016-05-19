<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Highlitingpath extends Controller_Ajax_Admin_Base_Highliting{

    use Controller_Ajax_Highliting2frontend;

    protected $_exeLogin = FALSE;

    protected $_pagination = FALSE;

    protected $_table = "Highliting_Path";

    protected $_typeORM = 'ORMGIS';

    protected $_url_multifield_foreignkey = 'highliting_path_id';

    protected $_inheritDatastructName = 'highliting_path';

    protected  $_multiFilesToSave = array(
        'front_image_highliting_poi' => 'Image_Highliting_Path',
    );

    protected $_image_uri ="/download/imagehighlitingpath/index/";
    protected $_image_thumb_uri ="/download/imagehighlitingpath/thumbnail/";

    public function action_update() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }

    public function action_delete() {
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





}