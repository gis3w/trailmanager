<?php defined('SYSPATH') or die('No direct script access.');

use Endroid\QrCode\QrCode;

class Controller_Admin_Download_Qrcode_Base extends Controller_Auth_Strict{
    
    protected $_pathToSave;
    protected $_orm;
    public $nameORM;


    public function before() {
        parent::before();
        
        $this->_orm = ORM::factory($this->nameORM,$this->request->param('id'));
        $this->_pathToSave = APPPATH.'../upload/'.$this->_pathToSave.'/';
        
        if(!file_exists($this->_pathToSave))
            mkdir ($this->_pathToSave);
        
        
        $this->_pathToSave = $this->_pathToSave.'qrecode_'.$this->nameORM.'_'.$this->_orm->id.'.png';
        
    }
    
    
    public function action_index(){
        $qrCode = new QrCode();
        $qrCode->setText("http://lturisticgis.gis3w.it");
        $qrCode->setSize(300);
        $qrCode->setPadding(10);
        $qrCode->render($this->_pathToSave);
    }


    
    public function after() {
        $this->response->send_file($this->_pathToSave);
    }
}