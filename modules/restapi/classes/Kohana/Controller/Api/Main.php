<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe astratta generica per i controlli semi autenticati: posono essere visibili sia da autenticati
 *  che non atuenticati
 *
 * @package    Kohana/restapi
 * @category   Controller
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2012 Gis3W
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 */

abstract class Kohana_Controller_Api_Main extends Kohana_Controller_REST{

    /**
     * Contiente la risposta in JSON
     * @var Object
     */
    public $jres;
    
    /**
     * Paramentro che indica il metodo da applicare
     * @var string
     */
    public $method;

    /**
     * Id del record da recuperare modificare e eliminare
     * @var Int $id
     */
    public $id;


    public $filtro;
    
    /**
     *Paramentro che conserva la modalità di esportazione del dato
     * xls,pdf,csv, ecc.
     * @var type 
     */
    public $mod_export;

    /**
     * Contiene la lista dei filtri che arrivano dal get
     * @var Array
     */
    protected $_filters = NULL;

    /**
     * Array che contine i campi per ordering che arriva dal get
     * @var Array
     */
    protected $_orderings = NULL;
    
    /**
     * Contiene il limite della ricerca dei risultati 
     * @var Interger 
     */
    protected $_limits = NULL;
    
    /**
     *  Indica se il risultato del getlist deve essere paginato oppure no
     * @var boolean
     */
    protected $_pagination = TRUE;


    /**
     * Utente si sistema gestofauna per procedure automatiche
     * @var ORM user 
     */
    public $sysUser = NULL;
    
    public $timestamp_java;
    


    /**
     * Oveloading del before per renderlo come Controller_Template
     * @return none
     */
    public function before()
    {
     
        $this->timestamp_java = time()*1000;
        // istanziamo l'utente di systema utile per i processi automatici
//        if(!Session::instance()->get('sysUser'))
//        {
//            $this->sysUser = ORM::factory('user',2);
//            Session::instance()->set('sysUser', $this->sysUser);
//        }
                       

        $this->jres = new Kohana_Jrespost();
        
        if(isset($_GET['pagination']))
            $this->_pagination = (bool)$_GET['pagination'];
        
        
        
                
                
        parent::before();

        // controllo del post nel caso degli action e update
        if(in_array($this->request->action(),array('create','update')))
        {

            // trasformazione dei PUT
            if($this->request->method() === 'PUT')
                parse_str ($this->request->body(),$_POST);
          
            $body = $this->request->body();

            //OR !isset($_POST) OR empty($_POST)
            if(!$body AND empty($_FILES))
                throw  HTTP_Exception::factory (500,'Il POST inviato è vuoto!');
             }

            // si filtrano i paramentri a seconda che siano stringe
        $this->id = $this->request->param('id');
        
        $this->filtro = $this->request->param('filtro');
        
        //nel caso export si creano gli oggetti principali se mod isset
        $this->mod_export = $this->request->param('mod_export');
      
         // si guarda prima se settato se è un numero o una stringa
        if($this->id)
        {
            if(is_numeric($this->id))
            {
                $this->id = (int)$this->id;
            }
                    
        }
        else
        {
           
            $this->id = 'list';
        }


        
    }

    /**
     * Implementazione del metodo GET REST che reindirizza la chiamata a seconda dell'id
     */
    public function action_index()
    {
        // switch dei metodi generico
        if(is_numeric($this->id))
        {
            $this->_get_item();
        }
        else
        {
            switch($this->id)
            {
                case 'list':
                    $this->_get_list();
                break;
            
                case 'token':
                case 'validation':
                    //si controlla se il metodo esiste e si avvia
                    $method = '_get_'.$this->id;
                    if(method_exists($this, $method))
                        $this->$method();
                break;
                
                
            }
        }        


    }

    /**
     * Metodo generico per il create REST
     */
    public function action_create(){}

    /**
     * Metodo generico per l'update REST
     */
    public function action_update()
    {
        //controllo sull'id se l'id non c'è
        if(!is_numeric($this->id))
            throw new HTTP_Exception_500 ('Attenzione per questa azione è necessario passare un id');

        //passati tutti i controlli si butta nel post i valori del body


    }

     /**
     * Metodo generico per il delete REST
     */
    public function action_delete()
    {
        //controllo sull'id se l'id non c'è
        if(!isset($this->id))
            throw new HTTP_Exception_500 ('Attenzione per questa azione è necessario passare un id');
    }


    /**
     * Oveloading dell'after per renderlo come Controller_Template
     * @return none
     */
    public function  after() {
            switch(Route::name($this->request->route()))
            {
                case 'export':
                    switch($this->mod_export)
                    {
                        case 'xls':

                            $objWriter = PHPExcel_IOFactory::createWriter($this->exls->objExport, 'Excel5');
                            $objWriter->save('php://output');
                            
                            //$this->response->send_file(TRUE,$this->exls->objExport->getProperties()->getTitle().'.xls',array('mime_type' => 'application/vnd.ms-excel'));
                            
                            $this->response->headers('Content-Type','application/vnd.ms-excel');
                            $this->response->headers('Content-Disposition','attachment;filename="'.$this->exls->objExport->getProperties()->getTitle().'.xls'.'"');
                            $this->response->headers('Cache-Control','max-age=0');
                        break;
                    }
                break;
            
                default:
                    $body = $this->jres;

                    $route_name = Route::name($this->request->route());
                    if($this->request->is_ajax() OR  substr($route_name, 0,7) === 'restapi' OR  substr($route_name, 0,4) === 'rest'  OR substr($route_name, 0,2) === 'jx') 
                    { 
                        // impostazione del tipo di risposta
                        $this->response->headers('Content-type','application/json');
                    }
                   
                    $this->response->body($body);
            }           
            

            return parent::after();
    }

    /**
     * Metodo per Access Controll List del controller
     */
    protected function _ACL(){}


    /**
     * Metodo protetto generale per rcpreo record
     */
    protected function _get_item(){}
    
    /**
     * Metodo protetto generale per rcupreo lista record
     */
    protected function _get_list(){}

    /**
     * Metodo per recuperare  i filtri dal get
     */
    protected function _get_filters()
    {
        if(isset($_GET['filter']))
        {
            if(!isset($this->_filters))
            {
                $this->_filters = array();
            }
            else
            {
                return TRUE;
            }
                
            
            
            $directoryController = $this->request->directory().'/'.$this->request->controller();
//            var_dump($directoryController);
//            exit;
           
            $filters = preg_split('/,/', $_GET['filter']) ;
            // scomposizione
            foreach($filters as $filter)
            {
                if(!empty($filter))
                {
                    list($key,$value) = preg_split('/:/',$filter);
                    // recupero filtro
                    // controllo sulla presenza dei valori
                    if($value)
                    {
                        
                                

                        /** CASI PARTICOLARI **/
                        switch($key)
                        {
                            // per ora va bene per controlle user
                            // TODO:  Da controllare e verificare meglio
                             
                            
                        
                            case 'user_id':
                                switch($directoryController)
                                {
                                   
                                    default:
                                       $this->_filters[] = array($key,$value);
                                }
                            break;
                        
                        
                        
                         case 'roles':
                                switch($directoryController)
                                {
                                    case "Ajax/Admin/User":
                                        $this->_filters[] = array('roles_users.role_id',"'".implode("','",preg_split('/\|/',$value))."'");
                                    break;


                                    default:
                                       $this->_filters[] = array($key,$value);
                                }
                            break;
                        

                        
                         case 'datefrom':
                         case 'dateto':
                                switch($directoryController)
                                {


                                    default:
                                       $this->_filters[] = array($key,$value);
                                }
                            break;
                        
                        
                            default :
                                $this->_filters[] = array($key,$value);
                                
                                  
                        }
                    }
                    
                }
                
            }
        }
    }

    /**
     * Metodo per applicare il filtro all'orm
     * @param ORM_instance $orm
     */
    protected function _apply_filters($orm)
    {  
        
        if($this->_filters)
        {
            foreach($this->_filters as $n => $filter)
            {
                list($col,$val) = $filter;

                if($subcol = strstr($col, '_datefrom',TRUE))
                {
                    // si controlla che esiste il _range_to
                    if(isset($this->_filters[$n+1]) AND strstr($this->_filters[$n+1][0], '_dateto',TRUE))
                    {
                        $orm->where($subcol,'>=',$val);
                    }
                    else
                    {
                        list($col,$val,$colexp,$valori) = $filter;
                        
                        $orm->where($colexp,'=',$valori);
                    }
                }
                elseif($subcol = strstr($col, '_dateto',TRUE))
                {
                    
                    if(isset($this->_filters[$n-1]) AND strstr($this->_filters[$n - 1][0], '_datefrom',TRUE))
                    {
                        $orm->where($subcol,'<=',$val);
                    }
                }
                elseif($col === 'SPECIAL_FILTER')
                {
                    list($filter,$value) = $val;
                    call_user_func($filter,$orm,$value);
                }
                else
                {
                    // si recuperano le caratteristiche del filtro
                    $met = '=';
                    // in questa maniera solo per NULL o null o Null
                    if(strtoupper($val) === 'NULL')
                    {
                        $met = 'IS';
                        $val = DB::expr($val);
                    }

                    $this->_filter_sch_criteria($col,$met,$val);
                    
                    $orm->where($col,$met,$val);
                }
                
                
                

            }

        }

    }
    
    protected function _filter_sch_criteria(&$col,&$met,&$val)
    {
        $forms_fields = Kohana::$config->load('forms_filters');
        
        $direcoryController = $this->request->directory().'/'.$this->request->controller();

        //$prio = ORM::factory('role')->get_levels();

        
        if(!isset($forms_fields[$direcoryController]) OR !isset($forms_fields[$direcoryController][$col]) OR empty($forms_fields[$direcoryController][$col]))
            return;
        
        $form = $forms_fields[$direcoryController][$col];
        
        //si sostituiscono le varie componenti se co sono
        $comps = array('col','met','val') ;
        foreach($comps as $comp)
        {
            if(isset($form[$comp]))
            {
                // si controlla prima se deve essere applicata qualche cosa
                $expr = preg_split("/@/", $form[$comp]);
                if(count($expr) > 1)
                {
                    $toReplace = $expr[1];
                    $toApply = $expr[0];
                }
                else
                {
                    $toReplace = $expr[0];
                }
                
                foreach($comps as $subcomp)
                    $toReplace = preg_replace ("/#".$subcomp."/", $$subcomp, $toReplace);
                
                if(count($expr) > 1)
                {
                    switch ($toApply)
                    {
                        case "DB::expr":
                            $toReplace = DB::expr($toReplace);
                        break;
                    }
                }
                
                $$comp = $toReplace;
            }
                
                
        }
        
//        for($i= 0; $i<=$prio[$this->user->main_role]; $i++)
//        {
//            if(!isset($form[$i]))
//                continue;
//            
//            foreach($form[$i] as $field)
//            {
//                
//                    $name = $field['name'];
//                    $sch_criteria = isset($field['sch_criteria']) ? $field['sch_criteria'] : NULL;
//                    ;
//
//                    if($name === $col AND $sch_criteria)
//                    {
//                        $sch_criteria =  preg_split('/\|/',$sch_criteria);
//                        
//                        if(isset($sch_criteria[1]))
//                            $param = $sch_criteria[1];
//                        
//                        $sch_criteria = $sch_criteria[0];
//
//                        switch($sch_criteria)
//                        {
//                            case 'levenshtein':
//                            case 'soundex':
//                            case 'Metaphone':
//
//                                $col = DB::expr($sch_criteria."($col,'$val')");
//                                $met = '<';
//                                $val = $param;
//
//
//                            break; 
//                        
//                            case 'ilike':
//                                
//                                $met = DB::expr('ilike');
//                                $val = "%$val%";
//                                
//                            break;
//                        }
//                    }
//                
//            } 
//            
//        }
    }
   
    
    /**
     * Metodo per lìaggiunta di limiti dei risultati
     */
    protected function _get_limits()
    {
        if(isset($_GET['limit']))
            $this->_limits =(int)$_GET['limit'];
    }

    /**
     * Metodo per applicare gli ordinatori all'orm
     * @param ORM_instance $orm
     */
    protected function _apply_orderings($orm)
    {      
        if($this->_orderings)
        {
            foreach($this->_orderings as $n => $order)
            {
                //TODO: da perfezionare per i vari tipi di ordine
                if(is_array($order))
                {
                    foreach($order as $ord)
                    {
                        $orm->order_by($ord[0],$ord[1]);
                    }
                }
                else
                {
                    $orm->order_by($order);
                }
                

            }
        }
    }

    /**
     * Metodo per applicare il limite di risultato alla ricerca
     * @param ORM_instance $orm 
     */
    protected function _apply_limits($orm)
    {
        if(!is_null($this->_limits))
                $orm->limit($this->_limits);
    }
    
    
    
    
    protected function _manage_orm_filter_page($ormstart,$exe = "find_all")
    {
        
        $res = $this->_manage_orm_filter($ormstart,$exe);
        return $res[0];
        
    }





    /**
     *  Metodo che serve per costruire l'orm e i risultati applicandogli i filtri, ordering e pagination
     *  per l'azione _get_list
     * @param Kohana::ORM $ormstart
     * @return Kohana::ORM
     */
    protected function _manage_orm_filter($ormstart,$exe = "find_all")
    {
        $orm = clone $ormstart;
        
        // recuperi dati get
        // filtering:
        $this->_get_filters();


        // se ci sono i filtri si fa un bel filtro
        $this->_apply_filters($orm);
        
        //limits:
        $this->_get_limits();

        //pagination:
        //$page = isset($_GET['page']) ? $_GET['page'] : NULL;
        $this->jres->data->tot_items = count($orm->$exe());

        if($this->_pagination)
        $pg = Pagination::factory(array(
            'total_items' =>  $this->jres->data->tot_items
        ));

         $orm = clone $ormstart;
         unset($ormstart);

        // si deve riapplicare i filtri e gli orfdini di nuovo purtroppo
        $this->_apply_filters($orm);

        $this->_apply_orderings($orm);

        // nel caso della presenza di un limite preimpostato la paginazione non ha più senso
        // si da la semplice lista non paginata
        if(!is_null($this->_limits))
        {
            
            $o = $orm->limit($this->_limits)->$exe();
            
            $this->jres->data->tot_items = count($o);
            
        }
        elseif ($this->_pagination)
        {
            // invio informazioni sul pagination
            $this->jres->data->page = $pg->current_page;

            $this->jres->data->offset = $pg->offset;

            $this->jres->data->items_per_page = $pg->items_per_page;
            
            $o = $orm->offset($pg->offset)->limit($pg->items_per_page)->$exe();
            
            
        }
        else
        {
             $o = $orm->$exe();
            
            $this->jres->data->tot_items = count($o);
            $this->jres->data->items_per_page = $this->jres->data->tot_items;
            $this->jres->data->page = 0;
            $this->jres->data->offset = 0;
        }

         return array($o,$orm);
    }

    /**
     * Metodo per filtrare e prendere i dati dai form orizzontali
     */
    protected function _filter_oriz_form_data($field = NULL) {}

    /**
     * Metodo per risposte con esito positivo ad esempio durante la conferma di salvataggio
     * @param String $msg messaggio di conferma
     */
    protected function _okmsg($msg = NULL)
    {
        $msg = isset($msg) ? __($msg) : GF::message('risposte_standard','okmsg');
        
        $this->jres->data->okmsg = $msg;
    }

    /**
     * Metodo per formattare in risposta di validazione
     * @param ORM_Validation_Exception $e
     */
    protected function _validation_error($e,$code = 10000)
    {
        $this->jres->status = 0;
        
        $this->jres->error->errcode = $code;

        
        //LE BASI DEGLI ERRORI DI VALIDAZIONE
        // 1) BASE 10000 ERRORI DI VALIDAZIONE PER INSERT E UPDATE
        // 2) BASE 20000 ERRORI DI VALIDAZIONE/CONFERMA PER DELETE
        
        switch($code)
        {                
            default:
                $this->jres->error->errmsg = 'Errori di validazione';
        }
        

        // riscostruzione dell'array errore per possibili casi external validation
        if($e instanceof ORM_Validation_Exception)
        {
            $errarr = $e->errors('validation');
        }
        // siprattuto nel caso di form orizzontali che hanno bisogno di validazione personalizzata
        elseif(is_array($e))
        {
            $errarr = $e;
        }

        if(isset($errarr['_external']))
        {
            $_external = $errarr['_external'];

            unset($errarr['_external']);

            $errarr = array_merge($errarr, $_external);
        }

        $this->jres->error->errdata = $errarr;

        // Azzeramento DATA ??? (Giusto??)
        $this->jres->data = array();

    }
    
    /**
     * Se sono dichiarati ed esistono applicano filtri specifici per i tipi di ruoli utentes
     * @param type $orm
     * @return type
     */
    protected function _apply_default_filters($orm)
    {
        if(!isset($this->user->main_role_id))
            return;
        $main_role_id = $this->user->main_role_id;
        $method = "_role_".$main_role_id."_filters";
        if(method_exists($this, $method))
                $this->$method($orm);
    }
    
    protected function _apply_default_filter($orm)
    {
        $this->_default_filter($orm);
    }
    
    protected function _default_filter($orm)
    {
        return;
    }
}
