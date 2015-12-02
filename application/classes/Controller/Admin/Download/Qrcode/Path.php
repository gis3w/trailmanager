<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Download_Qrcode_Path extends Controller_Admin_Download_Qrcode_Base{
    
   public $nameORM = 'Path';
   protected $_pathToSave = 'image/path';

   protected function _getPathToSave()
   {
      $this->_pathToSave = $this->_pathToSave.'qrcode_'.__($this->nameORM).'_'.$this->_orm->nome.'_'.date('Ymd-Hm',time()).'.png';
   }
}