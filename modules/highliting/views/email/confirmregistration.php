<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<p>
    Gentile <?php echo $user->user_data->nome.' '.$user->user_data->cognome ?>,
</p>

<p>
    Clicca sul link sotto indicato o copia il percorso nella barra degli indirizzi del tuo browser per convalidare la tua registrazione.
</p>
<p>
    <?php echo HTML::anchor($global_data['host_main'].'/confirmregistration/'.$user->hash_registration) ?>
</p>