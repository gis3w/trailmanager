<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Image_Area extends Datastruct_Image_Poi {
    
    protected $_nameORM = "Image_Area";
    
    public static $preKeyField = 'imagearea';

    protected function _columns_type() {
        $cls = parent::_columns_type();
        
               $cls["area_id"] = array_merge($cls["poi_id"],array(
                    'form_input_type' => self::HIDDEN,
                    "subform_table_show" => FALSE,
                )
            );
               
         $cls["file"] = array_merge($cls["file"],array(
                 'urls' => array(
                        'data' => 'jx/admin/upload/imagearea',
                        'delete' => 'jx/admin/upload/imagearea?file=$1',
                        'delete_options' => array(
                           '$1' => self::$preKeyField.'-file',
                        ),
                        'download' => 'admin/download/imagearea/index/$1',
                        'download_options' => array(
                            '$1' => self::$preKeyField.'-file',
                            ),
                         'show' => 'admin/download/imagearea/show/$1',
                         'show_options' => array(
                             '$1' => self::$preKeyField.'-file',
                         ),
                       'thumbnail' => 'admin/download/imagearea/thumbnail/$1',
                       'thumbnail_options' => array(
                            '$1' => self::$preKeyField.'-file',
                            ),
                    )
                )
            );
               
         return $cls;
      }             
  
}
