<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Changehighlitingtypology extends Controller_Ajax_Auth_Strict{

    protected $_pagination = FALSE;

    private $_fields = [
        'pt_inter',
        'strut_ric',
        'aree_attr',
        'insediam',
        'pt_acqua',
        'pt_socc',
        'percorr',
        'fatt_degr',
        'stato_segn',
        'tipo_segna',
    ];

    public function action_create() {
        
    }
    
    public function action_update() {
        
    }
    
    public function action_delete() {
        
    }
    
    protected function _get_item()
    {

        unset($this->jres->data->items);
        $this->jres->data = array(
            'pt_inter' => [
                'hidden' => [
                    'value' => FALSE,
                    ],
                'disabled' => [
                    'value' => FALSE,
                ],
                'value' => [
                    'items' => [
                        [
                            'id' =>1,
                            'description' => 'pippo',
                        ]
                    ],
                    'label_toshow' => '$1',
                    'label_toshow_params' => 'description',
                    'value_field' => 'id',
                ]
            ]
        );
    }

    protected function _get_list()
    {
        // hidden every fields
        unset($this->jres->data->items);
        foreach($this->_fields as $field)
            $this->jres->data[$field] = [
                'hidden' => [
                    'value' => TRUE,
                ],
                'disabled' => [
                    'value' => TRUE,
                ]
            ];
    }
  
}