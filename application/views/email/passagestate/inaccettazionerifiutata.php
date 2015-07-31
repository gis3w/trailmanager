<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<p>
    <?php
        if(isset($highliting->highliting_user->id))
        {
            $to = $highliting->highliting_user->user_data->nome.' '.$highliting->highliting_user->user_data->cognome;
        }
        else
        {
            $to = $highliting->anonimous_data->name.' '.$highliting->anonimous_data->surname;
        }
    ?>
    Gentile <?php echo $to ?>,
</p>

<p>
    La segnalazione n. <?php echo $highliting->id ?> da te inserita non è stata accettata:
</p>
<p>
    <?php echo $highliting->getNoteByToState($highliting->state)->note;  ?>
</p>
<div class='email_abstract'>
    <?php echo $highliting->email_abstract($mode='for_reporter'); ?>
</div>