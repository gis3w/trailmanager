<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Front_Highlitingpoi extends Datastruct_Highliting_Poi {

    public $filter = FALSE;
    public $unsetColumns = array('highliting_state_id','publish');

    
    public $groups = array(
        array(
            'name' => 'poi-data',
            'position' => 'block',
            'fields' => array(
                'id',
                'name',
                'surname',
                'email',
                'comune',
                'frazione',
                'via',
                'subject',
                'highliting_path_id',
                'highliting_typology_id',
                'pt_inter',
                'strut_ric',
                'aree_attr',
                'insediam',
                'pt_acqua',
                'pt_socc',
                'percorr',
                'fatt_degr',
                'stato_segn',
                'tipo_segna',
                'description',
                'front_image_highliting_poi',
                'the_geom'),
        ),
    ); 
    
    protected function _columns_type() {
        $columns = parent::_columns_type();
        
        $columns['description']['editor'] = FALSE;
        $columns['highliting_typology_id']['url_values'] = Kohana::$base_url.'jx/highlitingtypology';
        $columns['highliting_typology_id']['change'] = SAFE::setBaseUrl('jx/changehighlitingtypology/');
        $columns['the_geom']['form_show'] = FALSE;
        
        return $columns;
    }


    protected function _extra_columns_type()
    {

        if(!$this->user instanceof Model_User)
        {
            $anonimous_data = Datastruct::factory('Anonimous_Highlitings_Data')->render();
            $fct =  Arr::to_arrayjres($anonimous_data['fields'], 'get_item', 'anonimous_highlitings_data');  
        }
        

         
         
         $fct['front_image_highliting_poi'] = array_replace($this->_columnStruct, array(
                "data_type" => self::FILE,
                "table_show" => FALSE,
                'multiple' => TRUE,
                'urls' => array(
                           'data' => 'jx/upload/imagehighlitingpoi',
                           'delete' => 'jx/upload/imagehighlitingpoi?file=$1',
                           'delete_options' => array(
                              '$1' => self::$preKeyField.'-file',
                           ),
                           'download' => 'download/imagehighlitingpoi/index/$1',
                           'download_options' => array(
                               '$1' => self::$preKeyField.'-file',
                               ),
                           'thumbnail' => 'download/imagehighlitingpoi/thumbnail/$1',
                           'thumbnail_options' => array(
                               '$1' => self::$preKeyField.'-file',
                               ),
                       ),  
                'label' => __('Images to upload'),
             )
        );


         
        
        return $fct;
        
    }    
    
}
