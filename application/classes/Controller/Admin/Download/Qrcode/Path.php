<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Download_Qrcode_Path extends Controller_Admin_Download_Qrcode_Base{
    
   public $nameORM = 'Path';
   protected $_pathToSave = 'image/path';
}