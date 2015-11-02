<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Data_Poi extends Controller_Ajax_Data_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Poi";
    
    protected $_thumb_uri ="/download/imagepoi/thumbnail/"; 


    protected function _single_request_row($orm) {
        $toRes = $this->_get_base_data_from_orm($orm);
        $toRes['coordinates'] = __('Lat').': '.round($orm->lat,3).', '.__('Lon').': '.round($orm->lon,3);
        $toRes['quota'] = $toRes['quota'].' m';
        $toRes['last_update']  = $toRes['data_mod'] ? $toRes['data_mod'] : $toRes['data_ins'];
        return $toRes;

        #element to unset
        $unsetKeys = [
            'aree_attr',
            'aree_attr_current',
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
            'insediam',
            'insediam_current',
            'ip',
            'l',
            'note',
            'note_man',
            'nuova_segna',
            'nuova_segna_current',
            'prio_int',
            'prio_int_current',
            'pt_acqua',
            'pt_acqua_current',
            'pt_inter',
            'pt_inter_current',
            'pt_sooc',
            'pt_socc_current',
            'quali_ril',
            'rilev',
            'se',
            'stato_segn',
            'stato_segn_current',
            'stato_segna_current',
            'strut_ric',
            'strut_ric_current',
            'tipo_segna',
            'tipo_segna_current',
            'typologies',
        ];

        foreach ($unsetKeys as $k)
            unset($toRes[$k]);
        
    }

}