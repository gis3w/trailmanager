<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Front_Image_Poi extends Datastruct_Image_Poi {
    
   
     protected function _columns_type() {
         
         $cls = parent::_columns_type();
         
          $cls["file"] = array_merge($cls["file"],array(
                     'urls' => array(
                        'data' => 'jx/upload/imagepoi',
                        'delete' => 'jx/upload/imagepoi?file=$1',
                        'delete_options' => array(
                           '$1' => self::$preKeyField.'-file',
                        ),
                        'download' => 'download/imagepoi/index/$1',
                        'download_options' => array(
                            '$1' => self::$preKeyField.'-file',
                            ),
                        'thumbnail' => 'download/imagepoi/thumbnail/$1',
                        'thumbnail_options' => array(
                            '$1' => self::$preKeyField.'-file',
                            ),
                    ),  
                )
            );
        
            return $cls;
      }               
        
    
}
