<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe generica per l'esecuzione di comandi esterni
 *
 * @package    Gis3W
 * @category   Exe
 * @author     Walter Lorenzetti
 * @copyright  (c) 2013 Gis3W
 */

class Kohana_Exe
{
    
    protected $_ts;
    
    protected $_cmd;
    
    protected $_process;
    
    public $res;
    
    public $status;


    protected $_descriptorspec = array(
        0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
        1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
        2 => array("file", "/tmp/kohana-exe-error-output", "a") // stderr is a file to write to
     );
    
     protected $_cwd = '/tmp';
     
     public static function factory($cmd)
    {
         $class =  new Exe($cmd);
         
         return $class;
         
    }
    
    protected function __construct($cmd) {
        
        $this->_ts = time();
        $this->_cmd = $cmd;
       
    }
    
    public function run()
    {
        $this->_descriptorspec[2][1] .= "_".$this->_ts.'.txt';
        
        $this->_process = proc_open($this->_cmd, $this->_descriptorspec, $pipes, $this->_cwd);

        if (is_resource($this->_process)) {
           

            $this->res = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            // It is important that you close any pipes before calling
            // proc_close in order to avoid a deadlock
            $this->status = proc_close($this->_process);
            
            //if($this->status === -1 OR $this->status > 1)
                $this->error = file_get_contents ($this->_descriptorspec[2][1]);
            
            unlink($this->_descriptorspec[2][1]);
            
            return $this->res;

        }
    }
             
    
}