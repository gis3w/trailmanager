<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Data_Path extends Controller_Ajax_Data_Base{
    
    protected $_pagination = FALSE;
    
    
    protected $_thumb_uri ="/download/imagepath/thumbnail/";
    
    protected function _single_request_row($orm) {
        $toRes = $this->_get_base_data_from_orm($orm);

        // custome title for client
        $toRes['title'] = __('Path').' '.$toRes['nome'];
        
        $toRes['diff_q'] = $toRes['diff_q'].' m';
        $toRes['l'] = $toRes['l'].' m';
        $toRes['q_init'] = $toRes['q_init'].' m';
        $toRes['q_end'] = $toRes['q_end'].' m';
        $toRes['time'] = $toRes['time'].' min';
        $toRes['rev_time'] = $toRes['rev_time'].' min';
        $toRes['diff'] = $orm->difficulty->code.' - '.$orm->difficulty->description;
        $toRes['walkable'] = $orm->walkable->description;

        #element to unset
        $unsetKeys = [
            'bike',
            'cod_f1',
            'cod_f2',
            'coordxini',
            'coordxen',
            'coordyini',
            'coordyen',
            'data_ins',
            'data_mod',
            'ip',
            'nome',
        ];

        foreach ($unsetKeys as $k)
            unset($toRes[$k]);


        return $toRes;
        
    }


  
    
}