<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<div class="data_report">
    <span class="name"><?php echo $user->user_data->nome.' '.$user->user_data->cognome?></span>
    <span class="office"><?php if(isset($user->id)): ?> (<?php echo implode(',',  array_keys ($user->offices->find_all()->as_array('name'))) ?>)<?php endif ?></span>
</div>