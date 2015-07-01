<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Settheme extends Controller_Ajax_Auth_Strict{

    protected $_pagination = FALSE;

    public function action_delete() {
        
    }
    
    public function action_create() {
        
    }
    
    public function action_index() {
        
    }


    public function action_update() {
        
        $themeOld = ORM::factory('Theme')
                ->where('active','IS',DB::expr('true'))
                ->find();

        
        $themeNew = ORM::factory('Theme')
                ->where('name','=',$_POST['theme'])
                ->find();
        
        
        if(isset($themeNew->id))
        {
            $themeOld->active = FALSE;
            $themeOld->save();
            $themeNew->active = TRUE;
            $themeNew->save();
        }
    }
  
}