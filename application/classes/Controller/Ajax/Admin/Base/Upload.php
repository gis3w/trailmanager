<?php defined('SYSPATH') or die('No direct script access.');


abstract class Controller_Ajax_Admin_Base_Upload extends Controller_Ajax_Auth_Strict{
    
    protected $_upload_path;
    
    protected $_download_url = "/admin/download";
    
    protected $_delete_url;

    protected $_column_document_key;

    public $UploadHandler;
    
    public $uplload_options = array();


     protected function _ACL()
    {
         $this->_directory_ACL();
         $this->_controller_ACL();
     }
    
    public function before() {
        parent::before();
        
        $controller_down = SAFE::getDownloadClassByObj($this);

        
        if(isset($controller_down::$subpathUpload))
            $this->_subpath_upload = $controller_down::$subpathUpload;
        
        if(isset($controller_down::$keyField))
            $this->_column_document_key = $controller_down::$keyField;

        
        $this->_subpath_download = $this->_subpath_upload;
         if(isset($controller_down::$subpathDownload))
            $this->_subpath_download = "/".$controller_down::$subpathDownload;
        
        $this->_upload_path = Controller_Download_Base::UPLOADPATH;
        
        if(isset($this->_subpath_upload))
        {
            $this->_delete_url =$this->_upload_path.$this->_subpath_download."/";
            $this->_upload_path .= "/".$this->_subpath_upload."/";
            $this->_download_url .= $this->_subpath_download."/index/";
        }
            
        
        
        // si avvia l'uploader senza per initialize
        $this->uplload_options = array_merge ($this->uplload_options,array(
            'script_url' => "jx/admin/".$this->_delete_url,
            'upload_dir' => $this->_upload_path,
            'upload_url' => $this->_download_url,
            'max_file_size' => 10000000,
        ));

        error_log(print_r($this->uplload_options,true));
         error_log(print_r($this->_download_url,true));
         
//         exit;
        $this->UploadHandler = new UploadHandler($this->uplload_options,FALSE);
        
    }
    
    public function action_update() {
        
       $res =  $this->UploadHandler->post(FALSE);
       
       error_log(print_r($res,true));
      
       
        // devo aggiungere il controllo di eventuali errori di upload
       // si controlla che nella risposta ci siano degli errori:
       foreach($res[$this->uplload_options['param_name']] as $idx => $file)
          if(isset($file->error) AND $file->error !== '')
              $this->_errors[(string)$idx] = __($file->error);
          
          if(!empty($this->_errors))
               $this->_validation_error (array('file' => implode('; ',  array_values($this->_errors))));
          
           $this->jres->data = $res;

    }
    
    public function action_delete() {
        $res = $this->UploadHandler->delete(FALSE);
        $this->jres->data = $res;
    }

}