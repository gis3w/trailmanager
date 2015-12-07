<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Geo_Poi extends Controller_Ajax_Geo_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Poi";

    
    protected function _single_request_row($orm) {

        $row = $this->_get_geo_base_data_from_orm($orm);

        $row['title'] = $orm->idwp;

        return $row;


        
    }

    protected function _get_main_typology($orm)
    {
        return parent::_get_main_typology($orm);
    }


    
}