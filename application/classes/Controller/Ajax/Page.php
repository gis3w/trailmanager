<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Page extends Controller_Ajax_Base_Crud_GET{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Global_Page";
    
}