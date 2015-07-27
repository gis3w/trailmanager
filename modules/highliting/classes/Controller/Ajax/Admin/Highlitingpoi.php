<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Highlitingpoi extends Controller_Ajax_Admin_Base_Highliting{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Highliting_Poi";

    protected $_url_multifield_foreignkey = 'highliting_poi_id';


    
  
}