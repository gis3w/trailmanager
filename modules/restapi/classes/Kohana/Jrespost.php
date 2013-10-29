<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe per  la costruzione dell'oggetto risposta
 *
 * @package    Kohana/restapi
 * @category   Data
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2012 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 *
 */
class Kohana_Jrespost
{
        /**
         * @var Int
         */
        public $status = 1;

        /**
         * @var Object
         */
        public $error;

        /**
         * @var Object
         */
        public $data;

        public function  __construct()
        {
            // si avviano anche li altri oggetti della struttura del messaggio
            $this->error = new Kohana_Jrespost_Error();

            // istanziamento di un oggetto vuoto per data
            $this->data = new stdClass();

        }

        public function  __toString()
        {
            return json_encode($this);
        }

}
