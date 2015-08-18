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

abstract class Controller_Print_Base extends Controller_Base_Main {
    
    public $PHPPdf;
    protected $_pdfContent;
    
    protected $_pdfTemplate = "template_base.pdf";
    public $filename = "doc.pdf";

    protected $_xmlContentView;
    protected $_xmlHeader1View = 'print/header/header1';
    protected $_xmlCssView = 'print/stylesheet';
    public $xmlContent;
    public $xmlCss = NULL;

    protected $_mapFile;
    protected $_mapPath;
    protected $_tmp_dir;
    protected $_image_base_url;
    protected $_pdf_map_size;

    public function action_index()
    {
        // set the header
        View::set_global('header1',View::factory($this->_xmlHeader1View)
            ->set('background_color','#FF0000'));

        $printConfig = Kohana::$config->load('print');
        $this->_mapFile = $printConfig['mapfile'];
        $this->_mapPath = $printConfig['mappath'];
        $this->_tmp_dir = $printConfig['tmp_dir'];
        $this->_pdf_map_size = $printConfig['pdf_map_size'];
        $this->_image_base_url = $printConfig['image_base_url'];

    }
    
    protected function _initialize()
    {
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
            if(Kohana::$environment === Kohana::DEVELOPMENT)
            {
                $_file_dev = APPPATH."cache/print_xml_.".$_SERVER['REMOTE_ADDR'].".html";
                $_file_res = fopen($_file_dev, "wr");
                fwrite ($_file_res, $this->xmlContent);
                fclose ($_file_res);
            }
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
