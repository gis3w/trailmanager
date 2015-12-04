<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax_Admin_Upload_Pathzipfile extends Controller_Ajax_Admin_Base_Upload {


    public function before()
    {
        parent::before();
        // si avvia l'uploader senza per initialize
        $this->uplload_options = array(
            'param_name' => 'zipfile',
            'script_url' => "jx/admin/".$this->_delete_url,
            'upload_dir' => APPPATH.'cache/',
            'upload_url' => $this->_download_url,
            'max_file_size' => 10000000,
        );

        $this->UploadHandler = new UploadHandler($this->uplload_options,FALSE);
    }


    public function action_update() {

        $res =  $this->UploadHandler->post(FALSE);

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