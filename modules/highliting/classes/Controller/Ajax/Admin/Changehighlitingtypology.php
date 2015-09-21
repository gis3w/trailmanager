<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Changehighlitingtypology extends Controller_Ajax_Auth_Strict{

    protected $_pagination = FALSE;

    private $_fields = [
        'pt_inter' => [
            'highliting_typology_id' => '1',
            'ormName' => 'Pt_Inter_Poi',
        ],
        'strut_ric' => [
            'highliting_typology_id' => '1',
            'ormName' => 'Strut_Ric_Poi',
        ],
        'aree_attr' => [
            'highliting_typology_id' => '1',
            'ormName' => 'Aree_Attr_Poi',
        ],
        'insediam' => [
            'highliting_typology_id' => '1',
            'ormName' => 'Insediam_Poi',
        ],
        'pt_acqua' => [
            'highliting_typology_id' => '1',
            'ormName' => 'Pt_Acqua_Poi',
        ],
        'pt_socc' => [
            'highliting_typology_id' => '1',
            'ormName' => 'Pt_Socc_Poi',
        ],
        'percorr' => [
            'highliting_typology_id' => '3',
            'ormName' => 'Percorr_Segment',
        ],
        'fatt_degr' => [
            'highliting_typology_id' => '3',
            'ormName' => 'Fatt_Degr_Poi',
        ],
        'stato_segn' => [
            'highliting_typology_id' => '4',
            'ormName' => 'Stato_Segn_Poi',
        ],
        'tipo_segna' => [
            'highliting_typology_id' => '4',
            'ormName' => 'Tipo_Segna_Poi',
        ],
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
        $this->jres->data = [];
        $idTypology = $this->request->param('id');
        foreach ($this->_fields as $fieldName => $fieldParam ) {
            if($fieldParam['highliting_typology_id'] == $idTypology)
            {
                $values = ORM::factory($fieldParam['ormName'])->find_all();
                $items = [];
                foreach ($values as $value)
                    $items[] = [
                        'id' => $value->pk(),
                        'description' => $value->description,
                    ];
                $this->jres->data[$fieldName] = [
                    'hidden' => [
                        'value' => FALSE,
                    ],
                    'disabled' => [
                        'value' => FALSE,
                    ],
                    'value' => [
                        'items' => $items,
                        'label_toshow' => '$1',
                        'label_toshow_params' => [
                            '$1' => 'description',
                        ],
                        'value_field' => 'id',
                    ]
                ];
            }
            else
            {
                $this->jres->data[$fieldName] = [
                    'hidden' => [
                        'value' => TRUE,
                    ],
                    'disabled' => [
                        'value' => TRUE,
                    ]
                ];
            }
        }


    }

    protected function _get_list()
    {
        // hidden every fields
        unset($this->jres->data->items);
        $this->jres->data = [];
        foreach(array_keys($this->_fields) as $field)
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