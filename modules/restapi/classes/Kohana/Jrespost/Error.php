<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe per  la costruzione dell'oggetto errore della risposta JSON
 *
 * @package    Kohana/restapi
 * @category   Data
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2012 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class Kohana_Jrespost_Error
{
        /**
         * @var Int
         */
        public $errcode = 0;

        /**
         * @var String
         */
        public $errmsg = '';

        /**
         * Array contenente gli errori in particlare utile per la validazione
         * @var Array
         */
        public $errdata = array();


}
