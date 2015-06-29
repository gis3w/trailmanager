<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Filterdata extends Controller_Ajax_Auth_Strict{

    /**
     * Contiene il nome dell'orm richiesto
     * @var String
     */
    protected $_fname;


    public function before() {
        parent::before();
        
        if(!isset($_GET['f']))
            throw HTTP_Exception::factory (500,SAFE::message ('ehttp','500_no_f_filter'));
        $this->_fname = ucfirst($_GET['f']);
    }

    public function action_index() {
        
       $dataStruct = Filterdata::factory($this->_fname);
       
       $this->jres->data = $dataStruct->render();
    }

    
    
}