<?php defined('SYSPATH') OR die('No direct script access.');

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class Controller extends Kohana_Controller {
    
    public $dispatcher;
    
    public function before() {
        $lang = Session::instance()->get('lang');
        $lang_config = Kohana::$config->load('lang');
        
        // selezione del linguaggio
        if(isset($_REQUEST['lang']))
        {
            $lang = $_REQUEST['lang'];
        }
        else
        {
            if(!isset($lang))
                $lang = $lang_config['default'];
        }
         I18n::lang($lang);
         Session::instance()->set('lang', $lang);
         $other_langs = array_diff_key($lang_config['langs'], array_flip(array($lang)));
         View::bind_global('langs', $other_langs);
         
         
         // si istanzia anche la classe che gestisce e triggera gli eventi
         $this->dispatcher = new EventDispatcher();
         
        parent::before();
    }

}