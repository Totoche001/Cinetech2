<?php
class CONFIG
{
	public $DB_TYPE = 'mysql';					// Type de base de données : 'mysql' ou 'sqlite'
	public $DB_SERVER = 'localhost';			// Adresse du serveur de base de données (localhost ou adresse IP)
	public $DB_USER = 'user';					// Nom d'utilisateur pour la connexion à la base de données
	public $DB_PASSWORD = 'mypass';				// Mot de passe pour la connexion à la base de données
	public $DB_NAME = 'Name_database';			// Nom de la base de données à utiliser
	public $DB_TABLE = 'fmt_films';				// Nom de la table dans la base de données
	public $API_ACCESS_CODE = '';				// Code d'accès pour l'API (mot de passe pour accéder au serveur)
	public $API_VERSION = '2';					// Version de l'API utilisée
	public $POSTERS_DIRECTORY = 'affiche';		// Répertoire où sont stockées les affiches
}

// Création d'une instance de la classe CONFIG pour récupérer la configuration
$cfg = new CONFIG();

// Connexion à la base de données avec gestion des erreurs
try {
	if ($cfg->DB_TYPE == 'sqlite') {
		// Connexion à une base de données SQLite
		$db = new PDO('sqlite:' . $cfg->DB_NAME . '.sqlite3');
	} else {
		// Connexion à une base de données MySQL avec les paramètres fournis
		$db = new PDO('mysql:host=' . $cfg->DB_SERVER . ';dbname=' . $cfg->DB_NAME, $cfg->DB_USER, $cfg->DB_PASSWORD);
		// Définir l'encodage des caractères en UTF-8 pour éviter les problèmes d'affichage
		$db->query("SET NAMES UTF8");
	}
} catch (Exception $e) {
	// En cas d'erreur lors de la connexion, afficher un message d'erreur et arrêter le script
	die('Erreur : ' . $e->getMessage());
}
