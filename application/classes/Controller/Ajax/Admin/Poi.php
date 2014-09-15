<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Poi extends Controller_Ajax_Admin_Sheet_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Poi";
    
    protected $_url_multifield_postname = 'url_poi';
    protected $_url_multifield_nameORM = 'Url_Poi';
    protected $_url_multifield_foreignkey = 'poi_id';
    
  
}