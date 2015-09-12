<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<span class="label" style="background: <?php echo $state->color ?>"><?php echo (isset($withDescription) AND $withDescription) ? $state->description : $state->name ?></span>

