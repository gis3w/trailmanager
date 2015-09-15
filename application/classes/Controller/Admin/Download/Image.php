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
        
        //$this->image_file = Image::factory($this->path_to_file);
    }

    public function action_show()
    {
        // si recupera il nome del file
        $this->path_to_file = $this->_upload_path."/".$this->filename;

        if(!file_exists($this->path_to_file))
            throw HTTP_Exception::factory ('500', SAFE::message ('ehttp','500_no_file_in_fs'));
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
        if($this->request->action() == 'show')
        {
            $this->response->headers('Content-Type', File::mime_by_ext($this->path_to_file))
                ->headers('Cache-Control', 'max-age='.Date::HOUR.', public, must-revalidate')
                ->headers('Expires', gmdate('D, d M Y H:i:s', time() + Date::HOUR).' GMT')
                ->headers('Last-Modified', date('r', filemtime($this->path_to_file)));

            $myfile = fopen($this->path_to_file, "r");
            $this->response->body(fread($myfile,filesize($this->path_to_file)));
            fclose($myfile);
        }
        else
        {
            $this->response->send_file($this->path_to_file);
        }

    }


    
}