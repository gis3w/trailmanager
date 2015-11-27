<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<span class="label" background.color="<?php echo $state->color ?>" style="background: <?php echo $state->color ?>"><?php echo (isset($withDescription) AND $withDescription) ? $state->description : $state->name ?></span>

