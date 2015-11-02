<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Data_Path extends Controller_Ajax_Data_Base{
    
    protected $_pagination = FALSE;
    
    
    protected $_thumb_uri ="/download/imagepath/thumbnail/";
    
    protected function _single_request_row($orm) {
        $toRes = $this->_get_base_data_from_orm($orm);
        
        $toRes['modes'] = array_keys($orm->modes->find_all()->as_array('id'));
        
        $toRes['altitude_gap'] = $toRes['altitude_gap'].' m';
        $toRes['length'] = $toRes['length'].' m';
        $toRes['q_init_current'] = $toRes['q_init_current'].' m';
        $toRes['q_end_current'] = $toRes['q_end_current'].' m';
        $toRes['time_current'] = $toRes['time_current'].' min';
        $toRes['rev_time_current'] = $toRes['rev_time_current'].' min';
        $toRes['diff_current'] = $orm->difficulty_current->code.' - '.$orm->difficulty_current->description;
        $toRes['walkable_current'] = $orm->walkable_current->description;

        #element to unset
        $unsetKeys = [
            'time',
            'rev_time',
            'q_init',
            'q_end',
            'descriz',
            'bike',
            'cod_f1',
            'cod_f2',
            'coordxini',
            'coordxen',
            'coordyini',
            'coordyen',
            'data_ins',
            'data_mod',
            'diff',
            'em_natur',
            'em_paes',
            'ev_stcul',
            'inquiry',
            'ip',
            'l',
            'loc',
            'op_attr',
            'nome',
            'percorr_current',
            'percorr'
        ];

        foreach ($unsetKeys as $k)
            unset($toRes[$k]);


        return $toRes;
        
    }


  
    
}