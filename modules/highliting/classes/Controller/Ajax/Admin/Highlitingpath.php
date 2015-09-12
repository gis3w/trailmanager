<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Highlitingpath extends Controller_Ajax_Admin_Base_Highliting{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Highliting_Path";

    protected $_url_multifield_foreignkey = 'highliting_path_id';


    
  
}