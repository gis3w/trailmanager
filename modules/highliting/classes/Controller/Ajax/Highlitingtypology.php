<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Highlitingtypology extends Controller_Ajax_Base_Crud_GET{

    protected $_exeLogin = FALSE;
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Global_Highlitingtypology";

    protected function _default_filter($orm)
    {
        $section = ORM::factory('Section')->where('section','=','FRONTEND')->find();
        $orm->join('sections_highliting_typologies')
            ->on('sections_highliting_typologies.highliting_typology_id','=','highliting_typology.id')
            ->where('sections_highliting_typologies.section_id','=',$section->id);

    }
    
}