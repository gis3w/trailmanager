<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Download_Image extends Controller_Download_Base {
    
    public $filename;
    
    public static $subpathUpload = "image";
        
    protected $_nameORM = "Documenti_Veicolo";
    
    public static $keyField = 'image_id';
    
}