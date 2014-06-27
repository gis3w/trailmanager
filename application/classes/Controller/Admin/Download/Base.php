<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Download_Base extends Controller_Auth_Strict {
    
    protected $_mimetype;
    
    const UPLOADPATH= "upload";
    
    public static $subpathUpload;

    protected $_subpath_upload;
    
    protected $_keyfield;

    protected $_nameORM;
    
    public $filename;
    public $obg_id;
    public $path_to_file;
    
    protected function _ACL() {
        
        $this->_directory_ACL();
        $this->_controller_ACL();
    }


    public function before() {
        parent::before();
                
        $this->_initialize();
       
    }
    
    protected function _initialize()
    {
       // si riscustruisce il path
        $this->_upload_path = APPPATH."../".self::UPLOADPATH;
        
        if(isset(static::$keyField))
            $this->_keyfield = static::$keyField;
        
        if(isset(static::$subpathUpload))
            $this->_subpath_upload = static::$subpathUpload;
        
        //in caso di subpath_upload
        if(isset($this->_subpath_upload))
            $this->_upload_path .= "/".$this->_subpath_upload;

        
          
        if(!$this->request->param('file'))
            throw HTTP_Exception::factory ('500', SAFE::message ('ehttp','500_download_no_file_get'));
        
        $this->filename = $this->request->param('file');
        $this->obj_id = $this->request->param('obj_id');  
    }


    public function action_index(){

        
        // si controlla che il file sia presente si ain db che in filesystem
        // db check
        $apkdb  = ORM::factory($this->_nameORM)
                ->where($this->_keyfield,'=',$this->obj_id)
                ->where('file','=',$this->filename)
                ->find();
        // controllo se il download è diretto subito dopo l'upload
        $directDownload = FALSE;
        if(isset($_GET['set']) AND $_GET['set'] === 'uploading' )
            $directDownload = TRUE;
        
        if(is_null($apkdb->id) AND !$directDownload)
             throw HTTP_Exception::factory ('500', SAFE::message ('ehttp','500_no_file_in_db'));
        //filesystem check
        $this->path_to_file = $this->_upload_path."/".$this->obj_id."/".$this->filename;
//        var_dump(file_exists(addcslashes($this->path_to_file,' ')));
//        exit;
  
        if(!file_exists($this->path_to_file))
                 throw HTTP_Exception::factory ('500', SAFE::message ('ehttp','500_no_file_in_fs'));
        
    }
    
    
    public function after() {
        $this->response->send_file($this->path_to_file);
    }
    
    public static function getSubpathUpload(){
        return self::$subpathUpload;
    }
}