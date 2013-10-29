<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'items' => array(
         'datastruct' => 'company',
        'children' => array(
            array(
                'datastruct' => 'productionunit',
                'parent_orm_key' => 'azienda',
                'children' => array(
                    array(
                        'datastruct' => 'productionunit_dpitask',
                    ),
                    array(
                        'datastruct' => 'chemical',
                        'parent_orm_key' => 'unita_produttiva',
                        'children' => array(
                            array(
                                'datastruct' => 'document_chemical',
                                'parent_orm_key' => 'sostanza',
                            ),
                        ),
                    ),
                    array(
                        'datastruct' => 'plant',
                        'parent_orm_key' => 'unita_produttiva',
                        'children' => array(
                            array(
                                'datastruct' => 'document_plant',
                                'parent_orm_key' => 'impianto',
                            ),
                            array(
                                'datastruct' => 'expiration_plant',
                                'parent_orm_key' => 'impianto',
                            ),
                        ),
                    ),
                    array(
                        'datastruct' => 'document_productionunit',
                        'parent_orm_key' => 'unita_produttiva',
                    ),
                ),
            ),
            array(
                        'datastruct' => 'vehicle',
                        'parent_orm_key' => 'azienda_attuale',
                        'children' => array(
                            array(
                                'datastruct' => 'document_vehicle',
                                 'parent_orm_key' => 'veicolo',
                            ),
                            array(
                                'datastruct' => 'expiration_vehicle',
                                'parent_orm_key' => 'veicolo',
                            ),
                        ),
                    ),
                    array(
                        'datastruct' => 'machine',
                         'parent_orm_key' => 'azienda_attuale',
                        'children' => array(
                            array(
                                'datastruct' => 'document_machine',
                                 'parent_orm_key' => 'macchina',
                            ),
                            array(
                                'datastruct' => 'expiration_machine',
                                'parent_orm_key' => 'macchina',
                            ),
                        ),
                    ),
                    array(
                        'datastruct' => 'liftingmachine',
                        'parent_orm_key' => 'azienda_attuale',
                        'children' => array(
                            array(
                                'datastruct' => 'document_liftingmachine',
                                'parent_orm_key' => 'mezzo_sollevamento',
                            ),
                            array(
                                'datastruct' => 'expiration_liftingmachine',
                                'parent_orm_key' => 'mezzo_sollevamento',
                            ),
                        ),
                    ),
             array(
                'datastruct' => 'document_company',
                 'parent_orm_key' => 'azienda',
            ),
        ),
    
    
    )
);
       
