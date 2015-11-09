<?php defined('SYSPATH') OR die('No direct access allowed.');

$items = $jres->data->items;
?>

<table class="table table-stripped">
    <thead>
    <tr>
        <th><?php echo __('ID Path segment') ?></th>
        <th><?php echo __('Typology path segment')?></th>
        <th><?php echo __('Bottom typology path segment')?></th>
        <th><?php echo __('Difficulty typology path segment')?></th>
        <th><?php echo __('Walkable path segment')?></th>
        <th><?php echo __('Reduction walkable path segment')?></th>
        <th><?php echo __('Morfology path segment')?></th>
        <th><?php echo __('Ambient path segment')?></th>
        <th><?php echo __('GSM coverage path segment')?></th>



    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td>
                <a class="btn btn-default" href="/admin#pathsegment/<?php echo $item['gid'] ?>">
                    <span class="icon icon-link"></span> <?php echo $item['id_tratta'] ?>
                </a>
            </td>
            <td><?php echo SAFEDB::tbCache('Tp_Trat_Segment',$item['tp_trat'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Tp_Fondo_Segment',$item['tp_fondo'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Diff_Segment',$item['diff'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Percorr_Segment',$item['percorr'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Rid_Perc_Segment',$item['rid_perc'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Morf_Segment',$item['morf'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Ambiente_Segment',$item['ambiente'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Cop_Tel_Segment',$item['cop_tel'],'description','code') ?></td>

        </tr>
    <?php endforeach ?>
    </tbody>
</table>