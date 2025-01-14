<?php
	class CONFIG {
		public $DB_TYPE = 'mysql';					// mysql or sqlite
		public $DB_SERVER = 'localhost';			// localhost or IP address
		public $DB_USER = 'user';					// user name
		public $DB_PASSWORD = 'mypass';				// password
		public $DB_NAME = 'Name_database';			// name of database
		public $DB_TABLE = 'fmt_films';				// name of table
		public $API_ACCESS_CODE = '';				// password to access at server
		public $API_VERSION = '2';
		public $POSTERS_DIRECTORY = 'affiche';
	}

// Get configuration
$cfg = new CONFIG();

// Connection to database
try {
	if ($cfg->DB_TYPE == 'sqlite') {
		$db = new PDO('sqlite:' . $cfg->DB_NAME . '.sqlite3');
	} else {
		$db = new PDO('mysql:host=' . $cfg->DB_SERVER . ';dbname=' . $cfg->DB_NAME, $cfg->DB_USER, $cfg->DB_PASSWORD);
		$db->query("SET NAMES UTF8");
	}
} catch (Exception $e) {
	die('Erreur : ' . $e->getMessage());
}