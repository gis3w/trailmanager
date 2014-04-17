<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Download_Image extends Controller_Admin_Download_Base {
    
    public $filename;
    
    public static $subpathUpload = "image";
    
    public static $keyField = 'image_id';
    
    public function action_index()
    {
        
        // si recupera il nome del file
        $this->path_to_file = $this->_upload_path."/".$this->filename;
        
        if(!file_exists($this->path_to_file))
                 throw HTTP_Exception::factory ('500', SAFE::message ('ehttp','500_no_file_in_fs'));
        
        $this->image_file = Image::factory($this->path_to_file);
    }
    
    public function action_thumbnail()
    {
        
        // si recupera il nome del file
        $this->path_to_file = $this->_upload_path."/thumbnail/".$this->filename;
        
        if(!file_exists($this->path_to_file))
                 throw HTTP_Exception::factory ('500', SAFE::message ('ehttp','500_no_file_in_fs'));
        
        $this->image_file = Image::factory($this->path_to_file);
    }
    
    public function after() {
        $this->response->body($this->image_file);
    }
    
}