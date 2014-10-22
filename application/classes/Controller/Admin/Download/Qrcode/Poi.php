<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Download_Qrcode_Poi extends Controller_Admin_Download_Qrcode_Base{
    
   public $nameORM = 'Poi';
   protected $_pathToSave = 'image/poi';
}