<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Pathsegment extends Controller_Ajax_Admin_Sheet_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Path_Segment";


    protected function _single_request_row($orm)
    {
        $toRes = parent::_single_request_row($orm);
        $toRes['path_id'] = $orm->paths->find()->id;
        return $toRes;
    }
  
}