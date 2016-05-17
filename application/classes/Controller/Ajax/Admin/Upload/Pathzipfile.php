<?php defined('SYSPATH') or die('No direct script access.');

use Symfony\Component\Filesystem\Filesystem;

class Controller_Ajax_Admin_Upload_Pathzipfile extends Controller_Ajax_Admin_Base_Upload {

    protected $_upload_path;
    protected $_zipFile;
    protected $_dirZipFiles;

    public function before()
    {

        $this->_upload_path = APPPATH.'cache/';
        Controller_Ajax_Auth_Strict::before();
        // si avvia l'uploader senza per initialize
        $this->uplload_options = array(
            'param_name' => 'zipfile',
            'script_url' => "jx/admin/".$this->_delete_url,
            'upload_dir' => $this->_upload_path,
            'upload_url' => $this->_download_url,
            'max_file_size' => 50000000,
            'accept_file_types' => '/\.zip$/i',
        );

        $this->UploadHandler = new UploadHandler($this->uplload_options,FALSE);

    }


    public function action_update() {

        if(!isset($_POST['path_name']) OR $_POST['path_name'] == '')
        {
            $this->_errors['path_name'] = __('Path name doesn\'t be empty');
        }
        Kohana::$log->add(LOG::DEBUG,'Arriva upload');
        $res =  $this->UploadHandler->post(FALSE);
        Kohana::$log->add(LOG::DEBUG,'Pasato uplaod');

        // devo aggiungere il controllo di eventuali errori di upload
        // si controlla che nella risposta ci siano degli errori:
        foreach($res[$this->uplload_options['param_name']] as $idx => $file)
            if(isset($file->error) AND $file->error !== '')
                $this->_errors[(string)$idx] = __($file->error);

        if(!empty($this->_errors))
        {
            $this->_validation_error (array($this->uplload_options['param_name'] => implode('; ',  array_values($this->_errors))));
        }
        else
        {
            Kohana::$log->add(LOG::DEBUG,'Arriva loadxipfile');
            $this->_loadZipFile($res);
            Kohana::$log->add(LOG::DEBUG,'Passato loadzipfile');
            if(!empty($this->_errors))
            {
                $this->_validation_error (array($this->uplload_options['param_name'] => implode('; ',  array_values($this->_errors))));
            }
            // assuming directory name as path_name
            $this->_execTrailImport();
            $this->_deleteZipFiles();
        }

        $this->jres->data = $res;

        // reset cache
        SAFE::resetCache([
            'jx/data/everytype',
            'jx/geo/everytype',
            'jx/data/everytype',
            'jx/geo#it',
            'jx/media#it',
            'jx/data#it',
            'jx/geo#en',
            'jx/media#en',
            'jx/data#en',

        ]);

    }

    public function action_delete() {
        $res = $this->UploadHandler->delete(FALSE);
        $this->jres->data = $res;
    }

    protected function _deleteZipFiles()
    {
        Kohana::$log->add(LOG::DEBUG,'istanzina remove file');
        $filesystem = new Filesystem();
        Kohana::$log->add(LOG::DEBUG,'fine istanza remove file');
        try {
            Kohana::$log->add(LOG::DEBUG,'Arriva remove file');
            if(isset($this->_zipFile))
                $filesystem->remove($this->_zipFile);

            if(isset($this->_dirZipFiles))
                $filesystem->remove($this->_dirZipFiles);
            Kohana::$log->add(LOG::DEBUG,'passato remove file');

        }
        catch (Exception $e)
        {
            throw HTTP_Exception::factory(500,__('Si sono verificati i seguenti errori:').$e);
        }

    }


    /**
     * Execute extracted zip files.
     * @throws HTTP_Exception
     */
    protected function _execTrailImport()
    {
        try{
            Kohana::$log->add(LOG::DEBUG,'Arriva import');
            $this->_dirZipFiles = $this->_upload_path.$_POST['path_name'];
            $cmd = APPPATH.'../trail_import_data/tid.py '.$_POST['path_name'].' "'.ORM::factory('Itinerary',$_POST['itinerary'])->name.'"';
            $exe = Exe::factory($cmd);
            $res = $exe->run();
            Kohana::$log->add(LOG::DEBUG,'Passato import');


            if($exe->status === -1 OR $exe->status > 0)
            {
                Kohana::$log->add(LOG::DEBUG,'errore status import '.$exe->error);
                throw HTTP_Exception::factory(500,__('Si sono verificati i seguenti errori:').$exe->error);
            }

        }
        catch (Exception $e)
        {
            Kohana::$log->add(LOG::DEBUG,'errore import '.$e);
            throw HTTP_Exception::factory(500,__('Si sono verificati i seguenti errori:').$e);
        }


    }

    /**
     * Open zip file and extract
     * @param $res
     */
    protected function _loadZipFile($res)
    {
        // unzip file
        $zipFile = new ZipArchive();
        $this->_zipFile = $this->_upload_path.$res[$this->uplload_options['param_name']][0]->name;
        $res = $zipFile->open($this->_zipFile);
        if($res !== TRUE)
        {
            $this->_errors['zipfile'] = __('Problem on unzip file: '.$res);
            return;
        }

        $zipFile->extractTo($this->_upload_path);
        $zipFile->close();
    }



}