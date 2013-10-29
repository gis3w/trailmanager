<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Controller base per la stampa
 *
 * @package    Gis3W
 * @category   Controller
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2013 Gis3W
 * @license    http://kohanaframework.org/license
 */

abstract class Controller_Print_Base extends Controller_Auth_Strict {
    
    public $PHPPdf;
    protected $_pdfContent;
    
    protected $_pdfTemplate = "template_base.pdf";
    public $filename = "doc.pdf";

    protected $_xmlContentView;
    protected $_xmlCssView = 'print/stylesheet';
    public $xmlContent;
    public $xmlCss = NULL;
    
    protected function _ACL()
    {
        if(!$this->user->role->allow_capa('print-pdf'))
                    throw HTTP_Exception::factory(403,SAFE::message('capability','default',NULL,'print-pdf'));
        
         // recuper del controller
        $controller = preg_replace("/_/", "-",strtolower($this->request->controller())); ;
        $directory = preg_replace("/\//", "-",strtolower($this->request->directory()));

        $controller = $directory."-".$controller;
        
         if(!$this->user->role->allow_capa($controller))
                    throw HTTP_Exception::factory(403,SAFE::message('capability','default',NULL,$controller));

    }


    public function before() {
        parent::before();
        
        $this->_ACL();
        
        $this->PHPPdf =PHPPdf\Core\FacadeBuilder::create()                                             
                                               ->build();
        
        $this->_pdfTemplate = APPPATH."../public/pdf/".$this->_pdfTemplate;
        
        $this->_xmlContentView = View::factory($this->_xmlContentView);
        $this->_xmlContentView->template = $this->_pdfTemplate;
        if(isset($this->_xmlCssView))
            $this->_xmlCssView = View::factory($this->_xmlCssView);
    }
    
    public function after() {
        // si recuperano e si rendono i template
        try
        {
            $this->xmlContent = $this->_xmlContentView->render();
            if(isset($this->_xmlCssView))
                $this->xmlCss = $this->_xmlCssView->render();

        }
        catch (Exception $e)
        {
            
        }
        
        $this->_pdfContent = $this->PHPPdf->render($this->xmlContent, $this->xmlCss);

        $this->response->headers('Content-Type', 'application/pdf');
        $this->response->headers('Content-Disposition','attachment; filename=\''.$this->filename.'\'');
        echo $this->_pdfContent;
    }
}
