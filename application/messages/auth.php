<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Messaggi di risposta dei sistemi di autenticazione e recupero password
 */
 return array(

     "login_err" => "<b>Username</b> e/o <b>password</b> non validi<br /> Se il problema persiste contattare l'amminstratore di sistema",
     "change_err" => "Si sono verifica i seguenti errori:",
     "conf_registration_err" => "Spiacente ma questo utente non è stato ancora confermato, per eseguire il login confermate l'utente!",
     "conf_sendmail" => "Grazie per aver compilato il form di registrazione!<br /><br /> Controlla la tua casella postale, la mail inviata dal sistema conterr&agrave; le istruzioni per poter confermare la tua richiesta di iscrizione..",
     "conf_newpass" => "Il sistema ti ha inviato le istruzioni alla mail specificata per cambiare la tua password, se il messagio non arrivasse, contatta i responsabili del servizio!",
     "conf_change" => "La tua nuova password è stat salvata in database! Ora può effettuare nuovamente l'".HTML::anchor('/login', __('ACCESS')),
     "msg_change_newpass" => "Essendo la <u>prima volta che accedi al portale</u> (o nel caso ti siano state reimpostate le credenziali), ti è richiesto il <u>cambio della password provvisoria</u> che ti è stata fornita dall'Ente a cui hai fatto richiesta.<br /><br />
         Nel form sottostante inserisci la nuova password.",
     "pre_captcha" => "Inserisci i caratteri che vedi nell'imagine nella casella sottostante.",
     "pre_form" => array (
         'index' => '<p>Inserisci la  mail con cui ti sei registrato nell\'altante. Ti verr&agrave; inviata un mail con le istruzione per il cambio della password.</p>',
         'change' => '<p>Indica nella casella sottostante la tua nuova password</p>',
     ),
 );
 