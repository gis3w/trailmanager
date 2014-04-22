<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Image_Itinerary extends Datastruct_Image_Poi {
    
    protected $_nameORM = "Image_Itinerary";
    
    public static $preKeyField = 'imageitinerary';
    
    protected function _columns_type() {
        $cls = parent::_columns_type();
        
            $cls["itinerary_id"] = array_merge($cls["poi_id"],array(
                 'form_input_type' => self::HIDDEN,
                 "subform_table_show" => FALSE,
                )
            );
            
            $cls["file"] = array_merge($cls["file"],array(
                 'urls' => array(
                        'data' => 'jx/admin/upload/imageitinerary',
                        'delete' => 'jx/admin/upload/imageitinerary?file=$1',
                        'delete_options' => array(
                            '$1' => 'nome',
                        ),
                        'download' => 'admin/download/imageitinerary/$1/$2',
                        'download_options' => array(
                            '$1' => 'poi_id',
                            '$2' => 'nome',
                            ),
                    )
                )
            );
               
         return $cls;
      }             
  
}
