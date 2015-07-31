<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<div id="confirmregistration" class="confirmregistration maincontent">
    <?php if($registration):?>
    <p class="bg-success">Gentile <?php echo $user->user_data->nome.' '.$user->user_data->cognome ?> la tua registrazione è andata a buon fine. <?php echo HTML::anchor('/', 'Torna')?>Torna alla home per effettuare una segnalazione</p>
    <?php else: ?>
    <p class="bg-danger">Spiacente si è verificato un errore: il tuo hash di registrazione non è corretto. Prova a ripetere la registrazione.</p>
    <?php endif ?>
</div>

