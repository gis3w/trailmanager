<?php defined('SYSPATH') or die('No direct script access.');

/**
 *  Controller che mi da la pagina di cambio password con il primo login
 *
 * @package    Gis3W
 * @category   Controller
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2012 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

class Controller_Firstlogin extends Controller_Auth_Strict {
    
    public $tnavbar = NULL;
    public $tlogin = 'login';
    
    public function before() {
        parent::before();
        
        // se lo user ha gia cambiato la password fai il redirect alla home
//        if(isset($this->user->data_first_change_password))
//            HTTP::redirect('home');
        
        // si aggiunge il form di login
        $this->tlogin->form = View::factory('global/login/first');
    }
    
     public function action_index()
    {
        $this->tlogin->password = '';
        $this->tlogin->ripeti_password = '';
        $this->tlogin->form->change = $this->tlogin->change = FALSE;
        $errors = array();

        if ($_POST) {
            
            try
            {
            
            $valid = Validation::factory($_POST)
            ->labels(
                array(
                 'password' => __('Password'),
                 'ripeti_password' => __('Confirm Password'),
                )
            )
            ->rules(
                'password',array(
                    array('not_empty'),
                    array('min_length', array(':value', 3)),
                )
            )
            ->rules(
                'ripeti_password',array(
                    array('not_empty'),
                    array('min_length', array(':value', 3)),
                    array('matches', array(':validation', ':field', 'password')),
                )
            );
            
            if(!$valid->check())
                $errors = $valid->errors('validation');
            
            if(!empty($errors))
                throw new Validation_Exception($valid);
            
            // si salva la data di salvataggio della domanda
            $this->user->data_first_change_password = time();
            $this->user->password = $_POST['password'];
            $this->user->save();
            
            // si slogga
            Auth::instance()->logout(TRUE);
            
            $this->tlogin->form->change = $this->tlogin->change = TRUE;
            $this->tlogin->message = Kohana::message('auth', 'conf_change');
            
                        
            
            }
            catch (Validation_Exception $e)
            {
                 $this->tlogin->message = Kohana::message('auth', 'change_err');
                 $strErr = "<ul>";
                 foreach($errors as $field => $err)
                 {
                     $strErr .= "<li>".$err."</li>";
                 }
                 $strErr .= "</ul>";
                 $this->tlogin->message .= $strErr;
            }
        }

    }
}