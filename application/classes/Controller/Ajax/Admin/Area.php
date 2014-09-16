<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Area extends Controller_Ajax_Admin_Sheet_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Area";
    
    protected $_url_multifield_postname = 'url_area';
    protected $_url_multifield_nameORM = 'Url_Area';
    protected $_url_multifield_foreignkey = 'area_id';
}