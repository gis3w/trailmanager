<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<div id="resetpassword" class="resetpassoword ">
    <div class="login">
        <?php if(isset($change) AND !$change): ?>
        <div>
            <p>Gentile <?php echo $user->user_data->nome.' '.$user->user_data->cognome ?>(User: <?php echo $user->username ?>),</p>
            <p>Inserisci nel form sottostante la nuova password che desideri impostare.</p>
        </div>
        <?php endif; ?>
        <?php if (isset($message)):?>
            <div class="alert alert-error">
                <p><?php echo $message ?></p>
            </div>
        <?php endif; ?>
        <?php echo $form; ?>
    </div>
</div>
