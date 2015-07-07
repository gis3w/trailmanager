<?php defined('SYSPATH') OR die('No direct access allowed.');

$items = $jres->data->items;
?>


<table>
    <tr>
        <th><?php echo _('SE')?></th>
        <th><?php echo _('Origin description')?></th>
        <th><?php echo _('Link')?></th>
    </tr>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?php echo $item['se'] ?></td>
            <td><?php echo $item['descriz'] ?></td>
            <td><a class="btn" href="/admin/#path/<?php echo $item['id'] ?>"><?php echo $item['id'] ?></a></td>
        </tr>
    <?php endforeach ?>
</table>