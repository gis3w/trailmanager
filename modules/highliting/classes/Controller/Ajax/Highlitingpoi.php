<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Highlitingpoi extends Controller_Ajax_Base_Sheet{

    protected $_pagination = FALSE;

    protected $_datastruct = "Front_Highlitingpoi";

    protected $_url_multifield_foreignkey = 'highliting_poi_id';

    protected $_inheritDatastructName = 'highliting_poi';


}