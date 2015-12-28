<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Implementazione gesitione custom degli errori
 *
 * @package    Gis3W
 * @category   Exceptions
 * @author     Walter Lorenzetti, Lysender
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

//class Kohana_Exception extends Kohana_Api_Exception{}

class Kohana_Exception extends Kohana_Kohana_Exception
{
        public static function old_handler(Exception $e)
        {
            
        $attributes = array(
            'action'	=> 500,
            //'origuri'	=> rawurlencode(Arr::get($_SERVER, 'REQUEST_URI')),
            'message'	=> trim(rawurlencode($e->getMessage())),
            'file' => rawurldecode($e->getFile()),
            'line' => rawurldecode($e->getLine()),
        );



        if ($e instanceof Http_Exception)
        {
            $attributes['action'] = $e->getCode();
        }

        $req = Request::initial();
;
		// Throw errors when in development mode
            if (Kohana::$environment === Kohana::DEVELOPMENT)
            {

                    // si prova ad deviare l'errore su una risposta json se è una richiesta ajax
                    if(isset($req) AND (in_array(Route::name($req->route()),array('restapi')) OR substr($req->route(), 0,2) === 'jx'))
                    {
                        switch($req->route())
                        {
                            case "restapi":
                                $errUrl = "restapi/error";
                             break;

                             default:
                                $errUrl = "rest/error";
                        }
                        
                        echo Request::factory(Route::url($errUrl, $attributes))
                            ->execute()
                            ->send_headers()
                            ->body();
                         exit(1);
                    }
                    else
                    {
                        // gestione normale degli errori
                        parent::handler($e);
                    }
            }
            else
            {


                    if(is_object(Kohana::$log))
                            Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));

                    if(isset($req) AND (in_array(Route::name($req->route()),array('restapi')) OR substr($req->route(), 0,2) === 'jx'))
                    {
                        
                      switch($req->route())
                      {
                          case "restapi":
                              $errUrl = "restapi/error";
                           break;
                       
                           default:
                              $errUrl = "rest/error";
                      }
                        
                    echo Request::factory(Route::url($errUrl, $attributes))
                        ->execute()
                        ->send_headers()
                        ->body();
                     exit(1);

                    }                
                    else
                    {

                   // casso il superfluo
                   unset($attributes['origui']);
                   unset($attributes['file']);
                   unset($attributes['line']);
                   unset($attributes['message']);
                   

                        // Error sub request
                    echo Request::factory(Route::url('error', $attributes))
                            ->execute()
                            ->send_headers()
                            ->body();
                     exit(1);
                    }



            }
    }
    
    public static function response(Exception $e) {
        
        
        $attributes = array(
            'action'	=> 500,
            //'origuri'	=> rawurlencode(Arr::get($_SERVER, 'REQUEST_URI')),
            'message'	=> rawurlencode($e->getMessage()),
            //'message'	=> $e->getMessage(),
            'file' => rawurldecode($e->getFile()),
            'line' => rawurldecode($e->getLine()),
        );


        if ($e instanceof Http_Exception)
        {
            $attributes['action'] = $e->getCode();
        }

        $req = Request::initial();
        


		// Throw errors when in development mode
            if (Kohana::$environment === Kohana::DEVELOPMENT)
            {
                    
                    // si prova ad deviare l'errore su una risposta json se è una richiesta ajax
                    if(isset($req) AND ($req->route() instanceof Route) AND (in_array(Route::name($req->route()),array('restapi')) OR substr(Route::name($req->route()), 0,2) === 'jx'))
                    {
                        switch(Route::name($req->route()))
                        {
                            case "restapi":
                                $errUrl = 'restapi/error';
                            break;
                        
                            default:
                                $errUrl = 'rest/error';
                        }
                        echo Request::factory(Route::url($errUrl, $attributes))
                            ->execute()
                            ->send_headers()
                            ->body();
                         exit(1);
                    }
                    else
                    {
                        // gestione normale degli errori
                        return parent::response($e);
                    }
            }
            else
            {
                
                                
       

                    if(is_object(Kohana::$log))
                            Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));

                    if(isset($req) AND ($req->route() instanceof Route)  AND (in_array(Route::name($req->route()),array('restapi')) OR substr(Route::name($req->route()), 0,2) === 'jx'))
                    {
                        switch(Route::name($req->route()))
                        {
                            case "restapi":
                                $errUrl = 'restapi/error';
                            break;
                        
                           default:
                                $errUrl = 'rest/error';

                        }
                        
                        echo Request::factory(Route::url($errUrl, $attributes))
                            ->execute()
                            ->send_headers()
                            ->body();
                         exit(1);

                    }                
                    else
                    {
                        
                        

                        
                   // casso il superfluo
                   unset($attributes['origui']);
                   unset($attributes['file']);
                   unset($attributes['line']);
                   unset($attributes['message']);
                        // Error sub request
                   $attributes['controller'] = 'error';
                    echo Request::factory(Route::url('default', $attributes))
                            ->execute()
                            ->send_headers()
                            ->body();
                     exit(1);
                    }



            }
        
        
    }
}
