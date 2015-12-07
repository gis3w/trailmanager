<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Geo_Path extends Controller_Ajax_Geo_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Path";
    
    
    protected function _single_request_row($orm) {

        $row = $this->_get_geo_base_data_from_orm($orm);

        $row['title'] = $orm->nome;

        #add start and end point
        $geo = GEO::instance();
        $pt_start = $geo->pointFromToSRS([(float)$orm->coordxini,(float)$orm->coordyini],3004,4326);
        $pt_end = $geo->pointFromToSRS([(float)$orm->coordxen,(float)$orm->coordyen],3004,4326);

        $row['pt_start'] = [
            'type' => 'Point',
            'coordinates' => [
                $pt_start[0],
                $pt_start[1]
            ]
        ];
        $row['pt_end'] = [
            'type' => 'Point',
            'coordinates' => [
                $pt_end[0],
                $pt_end[1]
            ]
        ];

        return $row;

        
    }

  
    
}