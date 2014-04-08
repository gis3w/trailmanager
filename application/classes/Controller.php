<?php defined('SYSPATH') OR die('No direct script access.');

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class Controller extends Kohana_Controller {
    
    public $dispatcher;
    public $lang;
    public $lang_default;
    
    public function before() {
        
        $this->_set_lang();
         // si istanzia anche la classe che gestisce e triggera gli eventi
         $this->dispatcher = new EventDispatcher();
         
        parent::before();
    }

    protected function _set_lang()
    {
        $this->lang = Session::instance()->get('lang');
        $lang_config = Kohana::$config->load('lang');
        
        // selezione del linguaggio
        if(isset($_REQUEST['lang']))
        {
            $this->lang = $_REQUEST['lang'];
        }
        else
        {
            if(!isset($this->lang))
                $this->lang = $lang_config['default'];
        }
         I18n::lang($this->lang);
         Session::instance()->set('lang', $this->lang);
         $other_langs = array_diff_key($lang_config['langs'], array_flip(array($this->lang)));
         View::bind_global('langs', $other_langs);
    }
    
}