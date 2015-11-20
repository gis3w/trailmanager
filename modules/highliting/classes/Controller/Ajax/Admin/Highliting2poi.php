<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Highliting2poi extends Controller_Ajax_Auth_Strict{

    protected $_highliting;

    protected function _get_item()
    {
        //get highliting
        $this->_highliting = ORMGIS::factory('Highliting_Poi',$this->id);
        if(!$this->_highliting->pk())
            throw new Kohana_HTTP_Exception_500('No highliting id present into Database');

        #todo: check the typology grant

        #data typology
        $dataHighliting = $this->_highliting->as_array();


        $toRes = [
            'note' => $this->_highliting->description,
            'the_geom' => $this->_highliting->asgeojson_php
        ];

        $toRes +=  Arr::extract($dataHighliting,[
            'pt_inter',
            'strt_ric',
            'aree_attr',
            'insediam',
            'pt_acqua',
            'pt_socc',
            'fatt_degr',
            'stato_segn',
            'tipo_segn']);

        #set the path if it is present
        if(isset($this->_highliting->highliting_path_id))
        {
            $toRes['se'] = $this->_highliting->highliting_path->se;
        }

        #transform coordinate to 3304
        $this->_highliting->getLonLat(3004);
        $toRes['coord_x'] = $this->_highliting->lon;
        $toRes['coord_y'] = $this->_highliting->lat;

        $this->jres->data->tot_items = 1;
        $this->jres->data->page = 1;
        $this->jres->data->offset = 0;
        $this->jres->data->items_per_page = 1;
        $this->jres->data->items = $toRes;


    }

}