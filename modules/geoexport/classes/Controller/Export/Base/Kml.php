<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export_Base_Kml extends Controller_Export_Base_Auth_Nostrict{

    public $file;
    public $kml;
    public $global_bounds = array();


    public function after()
    {

    $this->response->headers('Content-Type','text/xml');
    if(isset($this->filename))
        $this->response->headers('Content-Disposition','attachment;filename="'.$this->filename.'"');
    $this->response->headers('Cache-Control','max-age=0');

    #try to get good format of document
    $dom = new DOMDocument("1.0");
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML(html_entity_decode($this->kml->render()));
        /*
    if (!$dom->schemaValidate(__DIR__.'/kml22gx.xsd')) {
        print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
        exit;
    }
        */
    $this->response->body($dom->saveXML());

        
    }
    

}