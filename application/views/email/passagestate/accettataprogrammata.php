<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<p>
    Gentile <?php echo $highliting->supervisor_user->user_data->nome.' '.$highliting->supervisor_user->user_data->cognome ?>,
</p>

<p>
    La segnalazione n. <?php echo $highliting->id ?> <i>programmata</i> ti è stata assegnata in qualità di Respondabile del procedimento:
</p>
<div class='email_abstract'>
    <?php echo $highliting->email_abstract(); ?>
</div>