<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'default' => array(
		'type'       => 'PDO_PG',
		'connection' => array(
			/**
			 * The following options are available for PDO:
			 *
			 * string   dsn         Data Source Name
			 * string   username    database username
			 * string   password    database password
			 * boolean  persistent  use persistent connections?
			 */
			'dsn'        => 'pgsql:host=localhost;dbname=dev_trail;port=5432;',
			'username'   => 'trail',
			'password'   => 'trail74',
			'persistent' => FALSE,
		),
		/**
		 * The following extra options are available for PDO:
		 *
		 * string   identifier  set the escaping identifier
		 */
		'table_prefix' => '',
                        'column_primary_key' => 'id',
                         'schema' => 'public',
		'charset'      => 'utf8',
		'caching'      => FALSE,    
	),
);
