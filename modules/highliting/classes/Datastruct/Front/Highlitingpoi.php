<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Front_Highlitingpoi extends Datastruct_Highliting_Poi {

    public $filter = FALSE;
    public $unsetColumns = array('the_geom','highliting_state_id','publish');
    
    public $groups = array(
        array(
            'name' => 'poi-data',
            'position' => 'block',
            'fields' => array('id','name','surname','email','comune','frazione','via','subject','highliting_typology_id','description','front_image_poi','the_geom'),
        ),
    ); 
    
    protected function _columns_type() {
        $columns = parent::_columns_type();
        
        $columns['description']['editor'] = FALSE;
        
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
