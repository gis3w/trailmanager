<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<div class="oldnotes">
    <?php foreach ($states as $state): 
        $from_state = View::factory('data/currentstate');
        $from_state->state = $state->from_state;
        $to_state = View::factory('data/currentstate');
        $to_state->state = $state->to_state
    ?>
    <?php if(!isset($n)) $n = 0; $n++; ?>
    <div class="odnotes_item oldnotes_item_<?php echo $n ?> panel panel-default">
        <div class="panel-heading">
        <?php echo $state->date ?> - <?php echo $state->user->user_data->nome.' '.$state->user->user_data->cognome ?> | 
                <?php echo $from_state ?> -> <?php echo $to_state ?>
        
        </div>
        <div class="panel-body">
        <?php echo $state->note ? $state->note : __('No note') ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>