<?php defined('SYSPATH') OR die('No direct access allowed.');

$items = $jres->data->items;
?>

<table class="table table-stripped">
    <thead>
    <tr>
        <th><?php echo __('IDWP') ?></th>
        <th><?php echo __('Point of interest class')?></th>
        <th><?php echo __('Accomodation building class')?></th>
        <th><?php echo __('Equip area class')?></th>
        <th><?php echo __('Village class')?></th>
        <th><?php echo __('Water point class')?></th>
        <th><?php echo __('Signage type class')?></th>
        <th><?php echo __('Rescue point class')?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td>
                <a class="btn btn-default" href="/admin#poi/<?php echo $item['idwp'] ?>">
                    <span class="icon icon-link"></span> <?php echo $item['idwp'] ?>
                </a>
            </td>
            <td><?php echo SAFEDB::tbCache('Pt_Inter_Poi',$item['pt_inter'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Strut_Ric_Poi',$item['strut_ric'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Aree_Attr_Poi',$item['aree_attr'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Insediam_Poi',$item['insediam'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Pt_Acqua_Poi',$item['pt_acqua'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Tipo_Segna_Poi',$item['tipo_segna'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Pt_Socc_Poi',$item['pt_socc'],'description','code') ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>