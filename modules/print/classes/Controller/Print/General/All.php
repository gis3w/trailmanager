<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Print_General_All extends Controller_Print_Base_Auth_Nostrict
{

    protected $_xmlContentView = 'print/general/all';
    protected $_xmlCssView = 'print/csstest2';
    protected $_pdfPageSize = "a4";


    public function action_index()
    {
        $map = new Mapserver();
        $this->_xmlContentView->url = $map->imageURL;

    }
}