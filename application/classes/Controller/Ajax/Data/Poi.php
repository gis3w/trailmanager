<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Data_Poi extends Controller_Ajax_Data_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Poi";
    
    protected $_thumb_uri ="/download/imagepoi/thumbnail/"; 


    protected function _single_request_row($orm) {
        $toRes = $this->_get_base_data_from_orm($orm);
        $toRes['title'] = $toRes['idwp'];
        $toRes['coordinates'] = __('Lat').': '.round($orm->lat,3).', '.__('Lon').': '.round($orm->lon,3);
        $toRes['quota'] = $toRes['quota'].' m';
        $toRes['last_update']  = $toRes['data_mod'] ? $toRes['data_mod'] : $toRes['data_ins'];
        return $toRes;

        #element to unset
        $unsetKeys = [
            'bike',
            'class_ril',
            'cod_f1',
            'cod_f2',
            'condmeteo',
            'coord_x',
            'coord_y',
            'data_ins',
            'data_mod',
            'data_ins',
            'data_ril',
            'idwp',
            'inquiry',
            'ip',
            'l',
            'note',
            'note_man',
            'nuova_segna',
            'prio_int',
            'quali_ril',
            'rilev',
            'se',
            'stato_segn',
            'tipo_segna',
            'typologies',
        ];

        foreach ($unsetKeys as $k)
            unset($toRes[$k]);
        
    }

}