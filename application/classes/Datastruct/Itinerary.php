<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Itinerary extends Datastruct {
    
    protected $_nameORM = "Itinerary";
    
    public $icon = 'suitcase';
    public $filter = TRUE;

    public $groups = array(
        array(
            'name' => 'company-data',
            'position' => 'left',
            'fields' => array('id','title','nome_commerciale','pdf_print_global_report'),
        ),
        array(
            'name' => 'company-address-data',
            'position' => 'left',
            'fields' => array('via','numero','citta','provincia','cap'),
        ),
         array(
            'name' => 'company-contact-data',
             'position' => 'left',
            'fields' => array('tel','fax','email','piva'),
        ),

        array(
            'name' => 'company-foreign-data',
            'position' => 'right',
            'fields' => array('codice_ateco','fondo_interprofessionale_id','azienda_gruppo_id'),
        ),
        
        array(
            'name' => 'company-admin02-data',
            'position' => 'right',
            'fields' => array('responsabile','maillist_users','checklist_users'),
            'roles' => array(13),
            
        )
    );
    
    public $title = array(
        "title_toshow" => "$1",
        "title_toshow_params" => array(
            "$1" => "ragione_sociale"
        )
    );


    protected function _menu_items()
    {
        return array(
            array(
                'label' => __('Company info'),
                'url' => NULL,
                'url_params' => NULL,
                'icon' => 'info-sign',
            ),
            array(
                'label' => __('Production units'),
                'url' => '/jx/productionunit?filter=unita_produttiva.azienda_id:$1',
                'url_params' => array(
                    '$1' => 'id'
                ),
                'icon' => 'building',
                'capability' => 'productionunit-list',
            ),
            array(
                'label' => __('Vehicles'),
                'url' => '/jx/vehicle?filter=azienda.id:$1',
                'url_params' => array(
                     '$1' => 'id'
                ),
                'icon' => 'truck',
                'capability' => 'vehicle-list',
            ),
            array(
                'label' => __('Machines'),
                'url' => '/jx/machine?filter=azienda.id:$1',
                'url_params' => array(
                     '$1' => 'id'
                ),
                'icon' => 'wrench',
                'capability' => 'machine-list',
            ),
            array(
                'label' => __('Lifting machines'),
                'url' => '/jx/liftingmachine?filter=azienda.id:$1',
                'url_params' => array(
                     '$1' => 'id'
                ),
                'icon' => 'arrow-up',
                'capability' => 'liftingmachine-list',
                'environments' => array(ENV_SICUREZZA_SUL_LAVORO)
            ),
             array(
                'label' => __('Chemicals'),
                'url' => '/jx/chemical?filter=azienda.id:$1',
                'url_params' => array(
                     '$1' => 'id'
                ),
                'icon' => 'beaker',
                 'capability' => 'chemical-list',
            ),
            array(
                'label' => __('Documents'),
                'url' => '/jx/document/company?filter=documenti_azienda.azienda_id:$1',
                'url_params' => array(
                     '$1' => 'id'
                ),
                'icon' => 'file',
                'capability' => 'document-company-list',
            ),
            array(
                'label' => __('Expirations'),
                'url' => '/jx/expiration/company?filter=scadenze_azienda.azienda_id:$1',
                'url_params' => array(
                     '$1' => 'id'
                ),
                  'icon' => 'bell',
                'capability' => 'expiration-company-list',
            ),
             array(
                'label' => __('Workers'),
                'url' => '/jx/user?set=available_for_company&filter=azienda_id:$1',
                'url_params' => array(
                     '$1' => 'id'
                ),
                 'icon' => 'group',
                 'capability' => 'user-list',
            ),
        );
    }
    
      protected function _columns_type() {
        
            return array(
                "azienda_gruppo_id" => array(
                    'table_show' => FALSE,
                    'form_input_type' => self::SELECT,
                    'foreign_mode' => self::SINGLESELECT,
                    'url_values' => '/jx/groupcompany',
                    'foreign_toshow' =>'$1',
                     'foreign_toshow_params' => array(
                        '$1' => 'nome',
                    ),
                    'table_show' => FALSE,
                    'capability' => 'groupcompany-list',
                ),
            );
      }
    
    protected function _extra_columns_type() {
        
        // si inserisce solo se è admin02 o admin01
        
        $exc = array();
        $exc['pdf_print_global_report']  = array_replace($this->_columnStruct,array(
                    'form_input_type' => self::BUTTON,
                    'input_class' => 'default',
                    'data_type' => 'pdf_print',
                    'url_values' => '/print/report/company/global/$1',
                    'url_values_params' => array(
                        '$1' => 'id',
                    ),
                    'description' => __('Download global report'),
                    'table_show' => FALSE,
                    'label' => __('Global report'),
                    'capability' => 'print-report-company-global',
                    'icon' => 'download-alt',
                    'editable' => array(
                        self::STATE_INSERT => FALSE,
                        self::STATE_UPDATE =>TRUE
                    ),
                )
        );
        return $exc;        
    }


    protected function _foreign_column_type() {
      
        $fct = array();
        
                
        $fcolumn = $this->_columnStruct;
        $fcolumn = array_replace($fcolumn,array(
            'data_type' => 'integer',
            'form_input_type' => self::SELECT,
            'foreign_mode' => self::MULTISELECT,
            'foreign_toshow' => '$1 - $2',
            'foreign_toshow_params' => array(
                '$1' => 'codice',
                '$2' => 'descrizione',
            ),
            'url_values' => '/jx/atecocode',
            'label' => __('Ateco code'),
             'description' => __('Select ateco code for company'),
             "table_show" => FALSE,
        ));
        
        $fct['codice_ateco'] = $fcolumn;
        
        $fcolumn = $this->_columnStruct;
        $fcolumn = array_replace($fcolumn,array(
            'data_type' => 'integer',
            'form_input_type' => self::SELECT,
            'foreign_mode' => self::SINGLESELECT,
            'url_values' => '/jx/interprofessionalfound',
            'default_value' => self::DEFAULT_VALUE_DATA,
             'foreign_toshow' =>'$1',
             'foreign_toshow_params' => array(
                '$1' => 'nome',
            ),
            'label' => __('Interprofessional found'),
             'description' => __('Select interprofessional found for company'),
             "table_show" => FALSE,
        ));
        
        $fct['fondo_interprofessionale_id'] = $fcolumn;

        $fcolumn = array_replace($this->_columnStruct,array(
            'data_type' => 'integer',
            'form_input_type' => self::SELECT,
            'foreign_mode' => self::SINGLESELECT,
            'url_values' => '/jx/user?set=only_admin02&filter=roles:13',
            'default_value' => self::DEFAULT_VALUE_DATA,
             'foreign_toshow' =>'$1',
             'foreign_toshow_params' => array(
                '$1' => 'nome',
            ),
            'label' => __('Company responsible'),
             "table_show" => FALSE,
            'roles' => array(13),
//             'editable' => array(
//                self::STATE_INSERT => FALSE,
//                self::STATE_UPDATE =>TRUE
//            ),
        ));
        
        $fct['responsabile'] = $fcolumn;
        
         $fct['maillist_users'] = array_replace($this->_columnStruct,array(
            'data_type' => 'integer',
            'form_input_type' => self::SELECT,
            'foreign_mode' => self::MULTISELECT,
            'url_values' => '/jx/user?set=available_for_company&filter=azienda_id:$1',
            'url_values_params' => array(
                '$1' =>array(
                    'level' => 'company',
                    'field' => 'id',
                ),      
            ),
            'default_value' => self::DEFAULT_VALUE_DATA,
             'foreign_toshow' =>'$1 $2',
             'foreign_toshow_params' => array(
                '$1' => 'nome',
                '$2' => 'cognome',
            ),
            'label' => __('Maillist report users'),
             "table_show" => FALSE,
            'roles' => array(13),
            'editable' => array(
                      self::STATE_INSERT => FALSE,
                      self::STATE_UPDATE =>TRUE
                  ),
             'form_show' => array(
                      self::STATE_INSERT => FALSE,
                      self::STATE_UPDATE =>TRUE
                  ),
        ));
         
         $fct['checklist_users'] = array_replace($fct['maillist_users'],array(
            'label' => __('Checklist mailing users'),
        ));

        return $fct;
        
    }
    
}
