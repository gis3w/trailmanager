<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<p>
    Gentile <?php echo $highliting->executor_user->user_data->nome.' '.$highliting->executor_user->user_data->cognome ?>,
</p>

<p>
    La segnalazione n. <?php echo $highliting->id ?> ti è stata assegnata in qualità di Esecutore del procedimento:
</p>
<div class='email_abstract'>
    <?php echo $highliting->email_abstract(); ?>
</div>