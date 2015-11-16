<?php defined('SYSPATH') OR die('No direct access allowed.');

$items = $jres->data->items;
?>

<table class="table table-stripped">
    <thead>
    <tr>
        <th><?php echo _('ID') ?></th>
        <th><?php echo _('Subject')?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td>
                <a class="btn btn-default" href="/admin#highliting_poi/<?php echo $item['id'] ?>">
                    <span class="icon icon-link"></span> <?php echo $item['id'] ?>
                </a>
            </td>
            <td><?php echo $item['subject'] ?></td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>