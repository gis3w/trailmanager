<?php defined('SYSPATH') OR die('No direct access allowed.');

$items = $jres->data->items;
?>


<table class="table table-stripped">
    <thead>
    <tr>
        <th><?php echo _('SE')?></th>
        <th><?php echo _('Origin description')?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td>
                <a class="btn btn-default" href="/admin#path/<?php echo $item['id'] ?>">
                    <span class="icon icon-link"></span> <?php echo $item['se'] ?>
                </a>
            </td>
            <td><?php echo $item['descriz'] ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>