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
        
        
        $this->_pathToSave = $this->_pathToSave.'qrcode_'.$this->nameORM.'_'.$this->_orm->id.'_'.Inflector::underscore($this->_orm->title).'.png';
        
    }
    
    
    public function action_index(){
        
        $url = HTTP_HOST.'/#'.strtolower($this->nameORM).'/'.$this->_orm->id;
        
        $qrCodeWidth = defined('QRCODE_WIDTH') ? QRCODE_WIDTH : 300;
        $qrCodePadding = defined('QRCODE_PADDING') ? QRCODE_PADDING : 10;
        
        $qrCode = new QrCode();
        $qrCode->setText($url);
        $qrCode->setSize($qrCodeWidth);
        $qrCode->setPadding($qrCodePadding);
        $qrCode->render($this->_pathToSave);
    }


    
    public function after() {
        $this->response->send_file($this->_pathToSave);
    }
}