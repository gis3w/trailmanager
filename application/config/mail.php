<?php defined('SYSPATH') or die('No direct access allowed.');

// impostazioni per il server di posta principale
// sistema di mail del sistema per le comunicazioni con gli enti
// gli enti usano la propria (sono gli unici gli altri usano quella del sistema)

return array(

	'pec'   => FALSE,
	'smtp'  => 'smtps.pec.aruba.it',
                   'port'  => 465,
	'method'    => 'tls',
	'username' => 'gis3w@pec.it',
	'password'  => 'ak6723wl',

);
