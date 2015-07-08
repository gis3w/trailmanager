<?php defined('SYSPATH') OR die('No direct access allowed.');

$items = $jres->data->items;
?>

<table class="table table-stripped">
    <thead>
    <tr>
        <th><?php echo _('ID') ?></th>
        <th><?php echo _('Typology path segment')?></th>
        <th><?php echo _('Bottom typology path segment')?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td>
                <a class="btn btn-default" href="/admin#pathsegment/<?php echo $item['gid'] ?>">
                    <span class="icon icon-link"></span> <?php echo $item['gid'] ?>
                </a>
            </td>
            <td><?php echo SAFEDB::tbCache('Tp_Trat_Segment',$item['tp_trat'],'description','code') ?></td>
            <td><?php echo SAFEDB::tbCache('Tp_Fondo_Segment',$item['tp_fondo'],'description','code') ?></td>

        </tr>
    <?php endforeach ?>
    </tbody>
</table>