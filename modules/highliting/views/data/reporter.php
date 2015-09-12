<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<div class="data_report">
    <span class="type">Registrato: </span>
    <span class="name"><?php echo $user->user_data->nome.' '.$user->user_data->cognome?></span>
    <span class="email"> (<?php echo $user->email ?>)</span>
</div>