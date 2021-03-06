<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Path extends Controller_Ajax_Admin_Sheet_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Path";
    
    #protected $_url_multifield_postname = 'url_path';
    #protected $_url_multifield_nameORM = 'Url_Path';
    #protected $_url_multifield_foreignkey = 'path_id';
    
    
    protected function _validation_url_multifiled() {
        
          // apply filter for numeric data
        // because validation is indipendent from ORM
        foreach(array('length','altitude_gap') as $field)
                if(isset($_POST[$field]))
                    $_POST[$field] = Filter::comma2point ($_POST[$field]);
        
        parent::_validation_url_multifiled();
    }

    protected function _data_edit() {
        
        parent::_data_edit();
        
    }

    protected function _single_request_row($orm)
    {
        $toRes = parent::_single_request_row($orm);
        $toRes['title'] = $orm->nome;
        return $toRes;
    }

    /**
     * Delete Pois and Path_segments related
     */
    protected function _delete_cascade()
    {
        // delete every image
        $pois = ORM::factory('Poi')->where('se','=',$this->_orm->se)->find_all();

        foreach($pois as $poi)
        {
            $images = $poi->images->find_all();
            foreach($images as $image)
                @unlink(APPPATH.'../upload/image/'.$image->file);
        }

        $poisQDelete = DB::delete('pois')
            ->where('se','=',$this->_orm->se)
            ->execute();
        $pathsQDelete = DB::delete('path_segments')
            ->where('se','=',$this->_orm->se)
            ->execute();
    }



}