<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Datastruct extends Controller_Ajax_Auth_Strict{

    /**
     * Contiene il nome dell'orm richiesto
     * @var String
     */
    protected $_tb;


    public function before() {
        parent::before();

        
        if(!isset($_GET['tb']))
            throw HTTP_Exception::factory (500,SAFE::message ('ehttp','500_no_tb_datastruct'));
        $this->_tb = Text::ucfirst($_GET['tb'],'_');
    }

    public function action_index() {
        
       $dataStruct = Datastruct::factory($this->_tb);
       
       $this->jres->data = $dataStruct->render();
    }
      
    
}