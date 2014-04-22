<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Image_Path extends Datastruct_Image_Poi {
    
    protected $_nameORM = "Image_Path";
    
    public static $preKeyField = 'imagepath';

    protected function _columns_type() {
        $cls = parent::_columns_type();
        
               $cls["path_id"] = array_merge($cls["poi_id"],array(
                    'form_input_type' => self::HIDDEN,
                    "subform_table_show" => FALSE,
                )
            );
               
         $cls["file"] = array_merge($cls["file"],array(
                 'urls' => array(
                        'data' => 'jx/admin/upload/imagepath',
                        'delete' => 'jx/admin/upload/imagepath?file=$1',
                        'delete_options' => array(
                            '$1' => 'nome',
                        ),
                        'download' => 'admin/download/imagepath/$1/$2',
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
