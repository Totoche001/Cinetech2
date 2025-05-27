<?php
// Démarre la mise en mémoire tampon de la sortie
ob_start();
/**
 * API de publication Filmotech
 * 
 * Cette classe gère l'API de publication pour l'application Filmotech.
 * Elle permet de gérer les requêtes HTTP, les autorisations, et les interactions avec la base de données.
 * 
 * @copyright 2013-2023 Pascal PLUCHON
 * @link https://www.filmotech.fr
 * @version 1.0
 */



// Inclusion du fichier de configuration
require_once("../include/config.inc.php");

/**
 * Classe principale de l'API
 * 
 * Cette classe contient toutes les méthodes et propriétés nécessaires
 * pour gérer les requêtes API, authentifier les utilisateurs,
 * et retourner les réponses appropriées.
 */
class API
{
	public $data = ""; // Données à retourner par l'API
	private $cfg; // Objet de configuration
	private $db = NULL; // Objet de connexion à la base de données

	public $_allow = array(); // Méthodes ou actions autorisées
	public $_content_type = "application/json"; // Type de contenu pour les réponses de l'API
	public $_request = array(); // Données de la requête reçue

	//private $_method = ""; // Méthode HTTP utilisée pour la requête (commentée car non utilisée)
	private $_code = 200; // Code de statut HTTP pour la réponse

	// Liste des services/méthodes autorisés pour l'API
	private $services = array(
		"check_server",
		"check_code",
		"get_config",
		"create_poster_directory",
		"get_movie_list",
		"create_table",
		"update_publishing_date",
		"publish"
	);

	// Constructeur : initialise les entrées, la configuration et la connexion à la base de données
	public function __construct()
	{
		$this->inputs();					// Nettoie et prépare les données d'entrée
		$this->cfg = new CONFIG(); 			// Initialise les paramètres de configuration de la base de données
		$this->dbConnect();					// Établit la connexion à la base de données
	}

	// Retourne le message correspondant au code de statut HTTP actuel
	public function get_status_message()
	{
		// Tableau associatif des codes de statut HTTP et leurs messages correspondants
		$status = array(
			100 => 'Continue', // La requête a été reçue, le client peut continuer
			101 => 'Switching Protocols', // Le serveur change de protocole selon la demande du client
			200 => 'OK', // La requête a réussi
			201 => 'Created', // Une nouvelle ressource a été créée avec succès
			202 => 'Accepted', // La requête a été acceptée mais pas encore traitée
			203 => 'Non-Authoritative Information', // Informations retournées provenant d'une source tierce
			204 => 'No Content', // La requête a réussi mais il n'y a pas de contenu à retourner
			205 => 'Reset Content', // La requête a réussi, le client doit réinitialiser la vue
			206 => 'Partial Content', // Le serveur retourne une partie seulement du contenu demandé
			300 => 'Multiple Choices', // Plusieurs options pour la ressource demandée
			301 => 'Moved Permanently', // La ressource a été déplacée de façon permanente
			302 => 'Found', // La ressource a été trouvée à une autre URI temporairement
			303 => 'See Other', // Voir une autre URI pour la réponse
			304 => 'Not Modified', // La ressource n'a pas été modifiée depuis la dernière requête
			305 => 'Use Proxy', // La ressource doit être accédée via un proxy
			306 => '(Unused)', // Code réservé, non utilisé
			307 => 'Temporary Redirect', // Redirection temporaire vers une autre URI
			400 => 'Bad Request', // La requête est mal formée ou invalide
			401 => 'Unauthorized', // Authentification requise ou échouée
			402 => 'Payment Required', // Réservé pour un usage futur
			403 => 'Forbidden', // Accès refusé à la ressource
			404 => 'Not Found', // Ressource non trouvée
			405 => 'Method Not Allowed', // Méthode HTTP non autorisée pour cette ressource
			406 => 'Not Acceptable', // Le contenu demandé n'est pas disponible dans un format acceptable
			407 => 'Proxy Authentication Required', // Authentification requise via un proxy
			408 => 'Request Timeout', // Le serveur a expiré en attendant la requête
			409 => 'Conflict', // Conflit avec l'état actuel de la ressource
			410 => 'Gone', // La ressource n'est plus disponible et ne le sera plus
			411 => 'Length Required', // La requête nécessite un en-tête Content-Length
			412 => 'Precondition Failed', // Une condition préalable donnée dans les en-têtes a échoué
			413 => 'Request Entity Too Large', // La requête est trop volumineuse pour être traitée
			414 => 'Request-URI Too Long', // L'URI de la requête est trop longue
			415 => 'Unsupported Media Type', // Le type de média de la requête n'est pas supporté
			416 => 'Requested Range Not Satisfiable', // La plage demandée n'est pas disponible
			417 => 'Expectation Failed', // L'attente indiquée dans l'en-tête Expect ne peut être satisfaite
			500 => 'Internal Server Error', // Erreur interne du serveur
			501 => 'Not Implemented', // Fonctionnalité non implémentée sur le serveur
			502 => 'Bad Gateway', // Mauvaise passerelle ou réponse invalide du serveur en amont
			503 => 'Service Unavailable', // Service temporairement indisponible
			504 => 'Gateway Timeout', // Délai d'attente dépassé par la passerelle
			505 => 'HTTP Version Not Supported' // Version HTTP non supportée par le serveur
		);
		// Retourne le message correspondant au code de statut actuel, ou 'Internal Server Error' si le code est inconnu
		return ($status[$this->_code]) ? $status[$this->_code] : $status[500];
	}
	// Nettoie les données d'entrée en fonction de la méthode HTTP utilisée
	private function inputs()
	{
		// Récupère la méthode HTTP de la requête (GET, POST, PUT, DELETE, etc.)
		switch ($this->get_request_method()) {
			case "POST":
				// Pour les requêtes POST, récupère le contenu JSON brut envoyé dans le corps de la requête
				// puis décode ce JSON en tableau associatif PHP
				// Ensuite, nettoie récursivement les données pour éviter les injections ou balises HTML
				$data = json_decode(file_get_contents('php://input'), true);
				$this->_request = $this->cleanInputs($data);
				break;
			case "GET":
				// Pour les requêtes GET, nettoie les paramètres passés dans l'URL ($_GET)
				$this->_request = $this->cleanInputs($_GET);
				break;
			case "DELETE":
				// Pour les requêtes DELETE, utilise également les paramètres GET pour récupérer les données
				// puis nettoie ces données
				$this->_request = $this->cleanInputs($_GET);
				break;
			case "PUT":
				// Pour les requêtes PUT, lit le contenu brut envoyé dans le corps de la requête
				// puis le parse en variables PHP via parse_str
				// Enfin, nettoie récursivement les données obtenues
				parse_str(file_get_contents("php://input"), $this->_request);
				$this->_request = $this->cleanInputs($this->_request);
				break;
			default:
				// Pour toute autre méthode HTTP non supportée, renvoie une réponse HTTP 406 Not Acceptable
				$this->response('', 406);
				break;
		}
	}

	// Fonction récursive pour nettoyer les données d'entrée
	// Elle supprime toutes les balises HTML et espaces superflus
	private function cleanInputs(mixed $data): array|string
	{
		// Initialise un tableau vide pour stocker les données nettoyées
		$clean_input = [];

		// Si les données sont un tableau, applique la fonction récursivement à chaque élément
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$clean_input[$k] = $this->cleanInputs($v);
			}
		} else {
			// Sinon, pour une donnée simple (string, int, etc.), convertit en chaîne,
			// supprime toutes les balises HTML avec strip_tags,
			// puis supprime les espaces en début et fin de chaîne avec trim
			$data = strip_tags((string)$data);
			$clean_input = trim($data);
		}

		// Retourne les données nettoyées, soit sous forme de tableau, soit de chaîne
		return $clean_input;
	}

	// Récupère la méthode HTTP utilisée pour la requête courante
	public function get_request_method()
	{
		// Accède à la variable serveur $_SERVER pour obtenir la méthode HTTP (ex: GET, POST)
		return $_SERVER['REQUEST_METHOD'];
	}
	// Envoie une réponse JSON avec les données fournies et le code HTTP spécifié
	public function response($data, $status)
	{
		// Définit le code de statut HTTP à utiliser dans la réponse ; par défaut 200 (OK) si aucun code fourni
		$this->_code = ($status) ? $status : 200;
		// Configure les en-têtes HTTP de la réponse, notamment le code de statut et le type de contenu
		$this->set_headers();
		// Affiche les données de la réponse (généralement une chaîne JSON)
		echo $data;
		// Termine l'exécution du script immédiatement après l'envoi de la réponse
		exit;
	}

	// Configure les en-têtes HTTP pour la réponse
	private function set_headers(): void
	{
		// Définit le code de statut HTTP de la réponse (ex: 200, 404, 500, etc.)
		http_response_code($this->_code);
		// Définit l'en-tête Content-Type avec le type de contenu et l'encodage UTF-8
		header("Content-Type: {$this->_content_type}; charset=utf-8");
	}

	// Vérifie la validité du code de sécurité (API_ACCESS_CODE) et la méthode d'accès (POST)
	private function check_code(): void
	{
		// Vérifie que la méthode HTTP utilisée est bien POST
		if ($this->get_request_method() !== "POST") {
			// Si la méthode n'est pas POST, prépare un tableau d'erreur avec le code 100
			$error = ['error_code' => "100"];
			// Envoie une réponse JSON avec le message d'erreur et le code HTTP 401 (Non autorisé)
			$this->response(json_encode($error), 401);
		}

		// Vérifie que le paramètre 'code' est présent dans la requête
		if (empty($this->_request['code'])) {
			// Si le paramètre 'code' est absent ou vide, prépare un tableau d'erreur avec le code 101
			$error = ['error_code' => "101"];
			// Envoie une réponse JSON avec le message d'erreur et le code HTTP 401 (Non autorisé)
			$this->response(json_encode($error), 401);
		}

		// Récupère la valeur du code fourni dans la requête
		$code = $this->_request['code'];
		// Compare le code fourni avec le code d'accès API configuré
		if ($code !== $this->cfg->API_ACCESS_CODE) {
			// Si les codes ne correspondent pas, prépare un tableau d'erreur avec le code 102
			$error = ['error_code' => "102"];
			// Envoie une réponse JSON avec le message d'erreur et le code HTTP 401 (Non autorisé)
			$this->response(json_encode($error), 401);
		}
	}
	// Connexion à la base de données
	private function dbConnect(): void
	{
		try {
			// Définition des options PDO pour gérer les erreurs et le mode de récupération des données
			$options = [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active les exceptions en cas d'erreur PDO pour faciliter le débogage
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Les résultats des requêtes seront retournés sous forme de tableaux associatifs
				PDO::ATTR_EMULATE_PREPARES => false // Désactive l'émulation des requêtes préparées pour utiliser les vraies requêtes préparées du SGBD, améliorant la sécurité et la performance
			];

			// Vérifie si le type de base de données configuré est SQLite
			if ($this->cfg->DB_TYPE === 'sqlite') {
				// Connexion à une base SQLite en spécifiant le chemin vers le fichier .sqlite3
				// Le chemin est relatif au dossier parent du script courant
				$this->db = new PDO(
					'sqlite:../' . $this->cfg->DB_NAME . '.sqlite3', // DSN SQLite avec chemin du fichier de base de données
					null, // Pas d'utilisateur requis pour SQLite
					null, // Pas de mot de passe requis pour SQLite
					$options // Options PDO définies ci-dessus
				);
			} else {
				// Sinon, connexion à une base MySQL avec les paramètres fournis dans la configuration
				$this->db = new PDO(
					"mysql:host={$this->cfg->DB_SERVER};dbname={$this->cfg->DB_NAME};charset=utf8mb4", // DSN MySQL incluant l'hôte, le nom de la base et le jeu de caractères UTF-8
					$this->cfg->DB_USER, // Nom d'utilisateur MySQL défini dans la configuration
					$this->cfg->DB_PASSWORD, // Mot de passe MySQL défini dans la configuration
					$options // Options PDO définies ci-dessus
				);
			}
		} catch (PDOException $e) {
			// En cas d'erreur lors de la tentative de connexion à la base de données
			// Capture l'exception PDOException pour récupérer le message d'erreur
			$error = ['error_msg' => $e->getMessage()]; // Prépare un tableau contenant le message d'erreur
			// Envoie une réponse JSON avec le message d'erreur et le code HTTP 412 (Precondition Failed)
			$this->response(json_encode($error), 412);
		}
	}
	// Create the MySQL Table
	private function create_table_mysql()
	{
		// Définition de la requête SQL pour créer la table si elle n'existe pas déjà
		// La table est nommée selon la configuration $this->cfg->DB_TABLE
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $this->cfg->DB_TABLE . '` ('
			. ' `ID` bigint(20) NOT NULL,' // Identifiant unique du film, clé primaire
			. ' `DateHeureMAJ` datetime NOT NULL default \'0000-00-00 00:00:00\',' // Date et heure de la dernière mise à jour
			. ' `TitreVF` varchar(255) NOT NULL default \'\',' // Titre en version française
			. ' `TitreVO` varchar(255) NOT NULL default \'\',' // Titre en version originale
			. ' `Genre` varchar(50) NOT NULL default \'\',' // Genre du film
			. ' `Pays` varchar(255) NOT NULL default \'\',' // Pays d'origine
			. ' `Annee` varchar(10) NOT NULL default \'\',' // Année de sortie
			. ' `Duree` int(11) NOT NULL default \'0\',' // Durée du film en minutes
			. ' `Note` int(11) NOT NULL default \'0\',' // Note attribuée au film
			. ' `Synopsis` text,' // Résumé du film
			. ' `Acteurs` text,' // Liste des acteurs
			. ' `Realisateurs` text,' // Liste des réalisateurs
			. ' `Commentaires` text,' // Commentaires divers
			. ' `Support` varchar(50) NOT NULL default \'\',' // Type de support (DVD, Blu-ray, etc.)
			. ' `NombreSupport` int(11) NOT NULL default \'0\',' // Nombre de supports
			. ' `Edition` varchar(255) NOT NULL default \'\',' // Edition particulière
			. ' `Zone` varchar(10) NOT NULL default \'\',' // Zone géographique du support
			. ' `Langues` varchar(255) NOT NULL default \'\',' // Langues disponibles
			. ' `SousTitres` varchar(255) NOT NULL default \'\',' // Sous-titres disponibles
			. ' `Audio` varchar(255) NOT NULL default \'\',' // Informations audio
			. ' `Bonus` text,' // Bonus inclus
			. ' `EntreeType` varchar(255) NOT NULL default \'\',' // Type d'entrée (achat, cadeau, etc.)
			. ' `EntreeSource` varchar(255) NOT NULL default \'\',' // Source de l'entrée
			. ' `EntreeDate` date NOT NULL default \'0000-00-00\',' // Date d'entrée dans la collection
			. ' `EntreePrix` float NOT NULL default \'0\',' // Prix d'entrée
			. ' `Sortie` varchar(10) NOT NULL default \'\',' // Sortie (prêt, vente, etc.)
			. ' `SortieType` varchar(255) NOT NULL default \'\',' // Type de sortie
			. ' `SortieDestinataire` varchar(255) NOT NULL default \'\',' // Destinataire de la sortie
			. ' `SortieDate` date NOT NULL default \'0000-00-00\',' // Date de sortie
			. ' `SortiePrix` float NOT NULL default \'0\',' // Prix de sortie
			. ' `PretEnCours` varchar(10) NOT NULL default \'\',' // Indique si le film est prêté actuellement
			. ' `FilmVu` varchar(5) NOT NULL default \'NON\',' // Indique si le film a été vu
			. ' `Reference` varchar(255) NOT NULL default \'\',' // Référence interne ou externe
			. ' `BAChemin` varchar(255) NOT NULL default \'\',' // Chemin vers la bande-annonce
			. ' `BAType` varchar(10) NOT NULL default \'\',' // Type de la bande-annonce
			. ' `MediaChemin` varchar(255) NOT NULL default \'\',' // Chemin vers le média
			. ' `MediaType` varchar(10) NOT NULL default \'\',' // Type du média
			. ' PRIMARY KEY (`ID`),' // Définition de la clé primaire sur la colonne ID
			. ' KEY `TitreVF` (`TitreVF`)' // Index sur la colonne TitreVF pour accélérer les recherches
			. ' ) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;'; // Moteur de stockage et encodage

		try {
			// Exécution de la requête SQL pour créer la table
			$this->db->query($sql);
		} catch (Exception $e) {
			// En cas d'erreur lors de la création de la table, préparation d'un message d'erreur
			$error = array('error_code' => "200", 'error_msg' => $e->getMessage());
			// Envoi de la réponse JSON avec le code HTTP 424 (Failed Dependency)
			$this->response(json_encode($error), 424);
		}

		// Si la création s'est bien passée, envoi d'une réponse de succès
		$success = array('status' => "OK");
		$this->response(json_encode($success), 200);
	}

	// Create the SQLite database table and index
	private function create_table_sqlite()
	{
		// Définition de la requête SQL pour créer la table SQLite avec les types et valeurs par défaut adaptés
		$sql =
			"CREATE TABLE " . $this->cfg->DB_TABLE . " ("
			. "ID integer NOT NULL PRIMARY KEY," // Identifiant unique, clé primaire SQLite
			. "DateHeureMAJ TimeStamp NOT NULL default '0000-00-00 00:00:00'," // Date et heure de mise à jour
			. "TitreVF varchar(255) NOT NULL default ''," // Titre en version française
			. "TitreVO varchar(255) default ''," // Titre en version originale
			. "Genre varchar(50) default ''," // Genre du film
			. "Pays varchar(255) default ''," // Pays d'origine
			. "Annee varchar(10) default ''," // Année de sortie
			. "Duree int(11) default '0'," // Durée en minutes
			. "Note int(11) default '0'," // Note attribuée
			. "Synopsis text ," // Résumé
			. "Acteurs text ," // Acteurs
			. "Realisateurs text ," // Réalisateurs
			. "Commentaires text ," // Commentaires
			. "Support varchar(50) default ''," // Support
			. "NombreSupport int(11) default '0'," // Nombre de supports
			. "Edition varchar(255) default ''," // Edition
			. "Zone varchar(10) default ''," // Zone
			. "Langues varchar(255) default ''," // Langues disponibles
			. "SousTitres varchar(255) default ''," // Sous-titres
			. "Audio varchar(255) default ''," // Audio
			. "Bonus text ," // Bonus
			. "EntreeType varchar(255) default ''," // Type d'entrée
			. "EntreeSource varchar(255) default ''," // Source d'entrée
			. "EntreeDate date default '0000-00-00'," // Date d'entrée
			. "EntreePrix float default '0'," // Prix d'entrée
			. "Sortie varchar(10) default ''," // Sortie
			. "SortieType varchar(255) default ''," // Type de sortie
			. "SortieDestinataire varchar(255) default ''," // Destinataire sortie
			. "SortieDate date default '0000-00-00'," // Date sortie
			. "SortiePrix float default '0'," // Prix sortie
			. "PretEnCours varchar(10) default ''," // Prêt en cours
			. "FilmVu varchar(5) default 'NON'," // Film vu ou non
			. "Reference varchar(255) default ''," // Référence
			. "BAChemin varchar(255) default ''," // Chemin bande-annonce
			. "BAType varchar(10) default ''," // Type bande-annonce
			. "MediaChemin varchar(255) default ''," // Chemin média
			. "MediaType varchar(10) default '');" // Type média
			// Création d'un index sur la colonne TitreVF pour accélérer les recherches
			. "CREATE INDEX films_idx ON " . $this->cfg->DB_TABLE . " (TitreVF ASC);";

		try {
			// Exécution de la requête SQL pour créer la table et l'index
			$this->db->query($sql);
			// Envoi d'une réponse JSON indiquant le succès de l'opération
			$success = array('status' => "OK");
			$this->response(json_encode($success), 200);
		} catch (Exception $e) {
			// En cas d'échec, tentative de réexécution de la requête (probablement une erreur)
			$this->db->query($sql);
			// Envoi d'une réponse JSON indiquant l'échec de l'opération
			$success = array('status' => "KO");
			$this->response(json_encode($success), 200);
		}
	}
	// Prépare la chaîne de caractères pour une requête SQL en fonction du type de base de données
	private function sql_escape($field)
	{
		// Si la base de données utilisée est SQLite
		if ($this->cfg->DB_TYPE == 'sqlite') {
			// SQLite nécessite que les apostrophes simples soient doublées pour être échappées correctement
			return str_replace('\'', '\'\'', $field);
		} else {
			// Pour les autres types de bases de données (ex : MySQL, PostgreSQL)
			// Utilisation de addslashes pour échapper les caractères spéciaux comme les apostrophes, guillemets, antislash, etc.
			return addslashes($field);
		}
	}

	// Ajoute un enregistrement et son affiche (si elle existe) dans la base de données
	private function add_record()
	{
		// Vérifie le code de sécurité avant toute opération
		$this->check_code();

		// Liste des champs à insérer dans la base de données
		// Chaque champ correspond à une information spécifique du film (titre, genre, durée, etc.)
		$champs = array("DateHeureMAJ", "TitreVF", "TitreVO", "Genre", "Pays", "Annee", "Duree", "Note", "Synopsis", "Acteurs", "Realisateurs", "Commentaires", "Support", "NombreSupport", "Edition", "Zone", "Langues", "SousTitres", "Audio", "Bonus", "EntreeType", "EntreeSource", "EntreeDate", "EntreePrix", "Sortie", "SortieType", "SortieDestinataire", "SortieDate", "SortiePrix", "PretEnCours", "FilmVu", "Reference", "BAChemin", "BAType", "MediaChemin", "MediaType");

		// Commence la construction de la requête SQL INSERT
		// Ajoute d'abord le nom de la table et l'ID
		$sql = 'INSERT INTO ' . $this->cfg->DB_TABLE . '(ID';

		// Ajoute tous les noms de champs à la requête
		foreach ($champs as $value) {
			$sql .= ', ' . $value;
		}

		// Commence la partie VALUES de la requête avec l'ID
		$sql .= ') VALUES(\'' . $this->_request['ID'] . '\'';

		// Ajoute chaque valeur de champ, en s'assurant qu'elle est correctement échappée
		// pour éviter les injections SQL
		foreach ($champs as $value) {
			$sql .= ', \'' . $this->sql_escape($this->_request[$value]) . '\'';
		}

		$sql .= ");";

		try {
			// Exécute la requête SQL pour ajouter l'enregistrement
			$data = $this->db->query($sql);
		} catch (Exception $e) {
			// En cas d'erreur, renvoie un code d'erreur et le message associé
			$tableau = array('error_code' => '300', 'error_msg' => $e->getMessage());
			$this->response(json_encode($tableau), 424);
		}
		// Ajoute l'image de l'affiche si elle est fournie
		$this->add_poster();
	}

	// Ajoute une image d'affiche pour l'enregistrement
	private function add_poster()
	{
		// Vérifie le code de sécurité
		$this->check_code();

		// Définit le chemin du répertoire des affiches
		$repertoire_affiches = '../' . $this->cfg->POSTERS_DIRECTORY;

		// Vérifie si une affiche a été fournie dans la requête
		if (isset($this->_request['Affiche'])) {
			// Décode les données de l'image qui sont en base64
			$affiche = base64_decode($this->_request['Affiche']);

			// Crée le nom de fichier pour l'affiche avec un format spécifique
			$filename = sprintf($repertoire_affiches . '/Filmotech_%05d.jpg', $this->_request['ID']);

			// Tente d'ouvrir le fichier en mode écriture binaire
			if (!$handle = fopen($filename, 'wb')) {
				// Renvoie une erreur si impossible d'ouvrir le fichier
				$error = array('error_code' => '301');
				$this->response(json_encode($error), 424);
			}

			// Écrit les données de l'image dans le fichier
			if (fwrite($handle, $affiche) === FALSE) {
				// Renvoie une erreur si l'écriture échoue
				$error = array('error_code' => '302');
				$this->response(json_encode($error), 424);
			}
			fclose($handle);

			// Change les permissions du fichier si demandé
			if (isset($this->_request['forceCHMOD'])) chmod($filename, 0777);
		}
	}

	// Vide le répertoire des affiches en supprimant tous les fichiers d'affiches
	private function empty_poster_directory()
	{
		// Vérifie le code de sécurité
		$this->check_code();

		// Définit le chemin du répertoire des affiches
		$repertoire_affiches = '../' . $this->cfg->POSTERS_DIRECTORY;

		// Parcourt tous les fichiers d'affiches (*.jpg) et les supprime un par un
		foreach (glob($repertoire_affiches . '/Filmotech*.jpg') as $filename) {
			unlink($filename);
		}
	}
	// Supprime un enregistrement et son image d'affiche associée (si elle existe)
	private function del_record()
	{
		// Vérifie le code de sécurité avant d'effectuer l'opération
		$this->check_code();

		// Construit la requête SQL DELETE pour supprimer l'enregistrement correspondant à l'ID
		$sql = "DELETE FROM " . $this->cfg->DB_TABLE . " WHERE ID = " . $this->_request['ID'];
		try {
			// Exécute la requête de suppression dans la base de données
			$this->db->query($sql);
		} catch (Exception $e) {
			// En cas d'erreur lors de la suppression, renvoie un message d'erreur avec le code 424
			$error = array('error_code' => '500', 'error_msg' => $e->getMessage());
			$this->response(json_encode($error), 424);
		}

		// Définit le chemin complet du fichier d'affiche associé à l'enregistrement
		$repertoire_affiches = '../' . $this->cfg->POSTERS_DIRECTORY;
		$filename = sprintf($repertoire_affiches . '/Filmotech_%05d.jpg', $this->_request['ID']);

		// Supprime le fichier d'affiche s'il existe sur le disque
		if (file_exists($filename)) {
			unlink($filename);
		}
	}

	// Met à jour la date de dernière publication (affichée dans la page de liste des films)
	private function update_publishing_date()
	{
		// Vérifie le code de sécurité avant d'effectuer l'opération
		$this->check_code();

		// Définit le chemin du fichier de mise à jour
		$filename = '../update.txt';

		// Tente d'ouvrir le fichier en mode écriture
		if (!$handle = fopen($filename, 'w')) {
			// Si impossible d'ouvrir le fichier, renvoie une erreur avec le code 424
			$error = array('error_code', '400');
			$this->response(json_encode($error), 424);
		}

		// Écrit la nouvelle date de mise à jour dans le fichier
		if (fwrite($handle, $this->_request['DateMAJ']) === FALSE) {
			// Si l'écriture échoue, renvoie une erreur avec le code 424
			$error = array('error_code', '401');
			$this->response(json_encode($error), 424);
		}

		// Ferme le fichier après l'écriture
		fclose($handle);

		// Renvoie une réponse de succès avec le code 200
		$success = array('status' => 'OK');
		$this->response(json_encode($success), 200);
	}
	// FONCTIONS DE L'API PUBLIQUE

	// Vérifie si le service est disponible et accessible
	function check_server()
	{
		// Prépare et envoie une réponse avec le statut OK pour indiquer que le serveur est opérationnel
		$success = array('status' => "OK");
		$this->response(json_encode($success), 200);
	}

	// Récupère la configuration de l'API et ses paramètres principaux
	protected function get_config()
	{
		// Vérifie le code de sécurité avant d'accéder à la configuration
		$this->check_code();

		// Prépare un tableau contenant les informations de configuration
		$tableau = array('status' => 'OK');
		$tableau["API_VERSION"] = $this->cfg->API_VERSION;        // Version actuelle de l'API
		$tableau["POSTERS_DIRECTORY"] = $this->cfg->POSTERS_DIRECTORY;  // Répertoire de stockage des affiches
		$tableau["DB_TABLE"] = $this->cfg->DB_TABLE;             // Nom de la table dans la base de données
		$tableau["PHP_VERSION"] = PHP_VERSION;                    // Version de PHP utilisée
		// Envoie les données de configuration au format JSON avec un code de succès
		$this->response(json_encode($tableau), 200);
	}

	// Crée le répertoire pour stocker les affiches des films s'il n'existe pas déjà
	function create_poster_directory()
	{
		// Vérifie le code de sécurité avant de créer le répertoire
		$this->check_code();

		$result = false;
		$repertoire_affiches = '../' . $this->cfg->POSTERS_DIRECTORY;
		$forceChmod = $this->_request['forceCHMOD'];

		// Vérifie si le répertoire existe déjà
		if (!is_dir($repertoire_affiches)) {
			// Tente de créer le répertoire des affiches
			$result = mkdir($repertoire_affiches);
			if (!$result) {
				// En cas d'échec de création, renvoie une erreur avec le code 424
				$error = array('error_code' => '201');
				$this->response(json_encode($error), 424);
			}
		}

		// Si l'option forceCHMOD est définie sur 'OUI', modifie les permissions du répertoire
		if ($forceChmod == 'OUI') {
			chmod($repertoire_affiches, 0777);
			// Renvoie un message de succès indiquant que les permissions ont été modifiées
			$success = array('status' => 'OK', 'message' => 'CHMOD forcé');
			$this->response(json_encode($success), 200);
		}

		// Renvoie une réponse de succès si tout s'est bien passé
		$success = array('status' => 'OK');
		$this->response(json_encode($success), 200);
	}

	// Crée la table dans la base de données en fonction du type de base de données configuré
	private function create_table()
	{
		// Vérifie le code de sécurité avant de créer la table
		$this->check_code();

		// Sélectionne la méthode appropriée en fonction du type de base de données configuré
		if ($this->cfg->DB_TYPE == 'sqlite') {
			$this->create_table_sqlite();    // Création pour SQLite
		} else {
			$this->create_table_mysql();     // Création pour MySQL
		}
	}
	// Récupère la liste des films avec leurs IDs et dates de mise à jour depuis la base de données
	private function get_movie_list()
	{
		// Vérifie le code de sécurité avant d'exécuter la requête
		$this->check_code();

		// Initialise un tableau de réponse avec le statut OK
		$tableau = array('status' => 'OK');

		// Exécute une requête SQL pour récupérer l'ID et la date/heure de mise à jour de chaque film
		$res = $this->db->query("SELECT ID, DateHeureMAJ FROM " . $this->cfg->DB_TABLE);

		// Parcourt les résultats et remplit le tableau avec l'ID comme clé et la date de mise à jour comme valeur
		foreach ($res as $row) {
			$tableau[$row['ID']] = $row['DateHeureMAJ'];
		}

		// Envoie la réponse au format JSON avec un code HTTP 200 (succès)
		$this->response(json_encode($tableau), 200);
	}

	// Fonction principale pour ajouter, mettre à jour ou supprimer des enregistrements de films
	private function publish()
	{
		// Vérifie le code de sécurité avant toute opération
		$this->check_code();

		// Si l'option ForceUpdate est activée, vide le répertoire des affiches
		if (!empty($this->_request['ForceUpdate'])) {
			$this->empty_poster_directory();
		}

		// Détermine l'action à effectuer en fonction du paramètre ACTION reçu
		switch ($this->_request['ACTION'] ?? '') {
			case 'ADD':
				// Ajoute un nouvel enregistrement dans la base de données
				$this->add_record();
				break;
			case 'UPDATE':
				// Pour une mise à jour : supprime d'abord l'ancien enregistrement puis ajoute le nouveau
				$this->del_record();
				$this->add_record();
				break;
			case 'DELETE':
				// Supprime l'enregistrement spécifié de la base de données
				$this->del_record();
				break;
		}

		// Prépare un tableau de réponse contenant les détails de l'action effectuée
		$tableau = [
			"action" => $this->_request['ACTION'] ?? null,    // Type d'action réalisée
			"TitreVF" => $this->_request['TitreVF'] ?? null, // Titre du film en version française
			"ID" => $this->_request['ID'] ?? null,           // Identifiant du film
		];

		// Envoie la réponse au format JSON avec un code HTTP 200 (succès)
		$this->response(json_encode($tableau), 200);
	}

	// PROCESSUS PRINCIPAL

	public function processApi()
	{
		// Récupère la fonction API demandée à partir des paramètres de la requête
		$rquest = $_REQUEST['rquest'] ?? null;

		if ($rquest !== null) {
			// Nettoie et formate le nom de la fonction demandée :
			// - strtolower : convertit en minuscules
			// - trim : supprime les espaces avant et après
			// - str_replace : supprime les caractères "/"
			$func = strtolower(trim(str_replace("/", "", $rquest)));
		} else {
			// Répond avec une erreur 400 si aucune requête n'est spécifiée
			$this->response('Invalid request', 400);
			return;
		}

		// Vérifie si la fonction demandée fait partie des services autorisés
		// définis dans le tableau $this->services
		if (in_array($func, $this->services)) {
			// Appelle dynamiquement la fonction demandée si elle existe
			$this->$func();
		} else {
			// Répond avec une erreur 404 si la fonction n'est pas trouvée
			// dans la liste des services disponibles
			$this->response('', 404);
		}
	}
}

// Crée une nouvelle instance de la classe API pour gérer les requêtes
$api = new API();

// Démarre le traitement de la requête API en appelant la méthode processApi()
// Cette méthode va analyser la requête, identifier l'action demandée et exécuter
// la fonction correspondante parmi les services disponibles
$api->processApi();

// Vide et envoie le contenu du tampon de sortie au client
// Cela permet de s'assurer que toutes les données sont bien transmises
// et que la mémoire tampon est correctement libérée
ob_end_flush();
?>