<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<table>
    <tr>
        <td><?php echo __('N.') ?></td><td><?php echo $highliting->id ?></td>
    </tr>
    <tr>
        <td><?php echo __('Creation date') ?></td><td><?php echo $highliting->data_ins ?></td>
    </tr>
    <tr>
        <td><?php echo __('Last update') ?></td><td><?php echo $highliting->data_mod ?></td>
    </tr>
    <?php if (isset($reporter)): ?>
    <tr>
        <td><?php echo __('Reporter') ?></td><td><?php echo $reporter ?></td>
    </tr>
    <?php endif ?>
    <?php if (isset($supervisor)): ?>
    <tr>
        <td><?php echo __('Supervisor') ?></td><td><?php echo $supervisor ?></td>
    </tr>
    <?php endif; ?>
    <tr>
         <td><?php echo __('Highlighting type') ?></td><td><?php echo $highliting_type ?></td>
    </tr>
    <tr>
         <td><?php echo __('Subject') ?></td><td><?php echo $highliting->subject ?></td>
    </tr>
    <tr>
         <td><?php echo __('Pictures number') ?></td><td><?php echo $highliting->pictures_number() ?></td>
    </tr>
</table>