<?php
ob_start();
/*
			Filmotech publishing API
			(c) 2013-2023 by Pascal PLUCHON
			https://www.filmotech.fr
 	*/

require_once("../include/config.inc.php");

class API
{

	public $data = ""; // Data to be returned by the API
	private $cfg; // Configuration object
	private $db = NULL; // Database connection object

	public $_allow = array(); // Allowed methods or actions
	public $_content_type = "application/json"; // Content type for API responses
	public $_request = array(); // Request data

	//private $_method = ""; // HTTP method used for the request
	private $_code = 200; // HTTP status code for the response

	// List of allowed methods for the API
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

	// Constructor: initializes input, configuration, and database connection
	public function __construct()
	{
		$this->inputs();					// Clean up input data
		$this->cfg = new CONFIG(); 			// Init database parameters
		$this->dbConnect();					// Initiate Database connection
	}

	// Return status code message
	public function get_status_message()
	{
		// Array of HTTP status codes and their corresponding messages
		$status = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '(Unused)',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'
		);
		// Return the message corresponding to the current status code, or 500 if not found
		return ($status[$this->_code]) ? $status[$this->_code] : $status[500];
	}

	// Clean up input data based on the HTTP request method
	private function inputs()
	{
		switch ($this->get_request_method()) {
			case "POST":
				// Decode JSON input for POST requests and clean it
				$data = json_decode(file_get_contents('php://input'), true);
				$this->_request = $this->cleanInputs($data);
				break;
			case "GET":
				// Clean GET parameters
				$this->_request = $this->cleanInputs($_GET);
				break;
			case "DELETE":
				// Clean GET parameters for DELETE requests as well
				$this->_request = $this->cleanInputs($_GET);
				break;
			case "PUT":
				// Parse and clean input data for PUT requests
				parse_str(file_get_contents("php://input"), $this->_request);
				$this->_request = $this->cleanInputs($this->_request);
				break;
			default:
				// Respond with 406 Not Acceptable for unsupported methods
				$this->response('', 406);
				break;
		}
	}

	private function cleanInputs(mixed $data): array|string
	{
		// Initialize an empty array to hold cleaned input data
		$clean_input = [];

		// If the input data is an array, recursively clean each element
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$clean_input[$k] = $this->cleanInputs($v);
			}
		} else {
			// If the input is a string or other type, strip HTML tags and trim whitespace
			$data = strip_tags((string)$data);
			$clean_input = trim($data);
		}

		// Return the cleaned input data
		return $clean_input;
	}

	// Get the HTTP request method (GET, POST, PUT, DELETE, etc.)
	public function get_request_method()
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	// Send a JSON response with the given data and HTTP status code
	public function response($data, $status)
	{
		// Set the HTTP status code, default to 200 if none provided
		$this->_code = ($status) ? $status : 200;
		// Set the HTTP headers for the response
		$this->set_headers();
		// Output the response data
		echo $data;
		// Terminate the script execution
		exit;
	}

	// Set HTTP headers for the response, including status code and content type
	private function set_headers(): void
	{
		http_response_code($this->_code);
		header("Content-Type: {$this->_content_type}; charset=utf-8");
	}

	// Check the security code (API_ACCESS_CODE) and the access method (POST)
	private function check_code(): void
	{
		// Verify that the request method is POST
		if ($this->get_request_method() !== "POST") {
			// If not POST, return error code 100 with 401 Unauthorized status
			$error = ['error_code' => "100"];
			$this->response(json_encode($error), 401);
		}

		// Check if the 'code' parameter is present in the request
		if (empty($this->_request['code'])) {
			// If 'code' is missing, return error code 101 with 401 Unauthorized status
			$error = ['error_code' => "101"];
			$this->response(json_encode($error), 401);
		}

		// Retrieve the provided code from the request
		$code = $this->_request['code'];
		// Compare the provided code with the configured API access code
		if ($code !== $this->cfg->API_ACCESS_CODE) {
			// If codes do not match, return error code 102 with 401 Unauthorized status
			$error = ['error_code' => "102"];
			$this->response(json_encode($error), 401);
		}
	}

	// Database connection
	private function dbConnect(): void
	{
		try {
			// Définition des options PDO pour la gestion des erreurs et le mode de récupération des données
			$options = [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Active les exceptions en cas d'erreur PDO
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Les résultats seront récupérés sous forme de tableau associatif
				PDO::ATTR_EMULATE_PREPARES => false // Désactive l'émulation des requêtes préparées pour utiliser les vraies requêtes préparées du SGBD
			];

			// Vérifie si le type de base de données configuré est SQLite
			if ($this->cfg->DB_TYPE === 'sqlite') {
				// Connexion à une base SQLite en spécifiant le chemin du fichier .sqlite3
				$this->db = new PDO(
					'sqlite:../' . $this->cfg->DB_NAME . '.sqlite3', // Chemin vers le fichier SQLite
					null, // Pas d'utilisateur pour SQLite
					null, // Pas de mot de passe pour SQLite
					$options // Options PDO définies précédemment
				);
			} else {
				// Sinon, connexion à une base MySQL avec les paramètres fournis
				$this->db = new PDO(
					"mysql:host={$this->cfg->DB_SERVER};dbname={$this->cfg->DB_NAME};charset=utf8mb4", // DSN MySQL avec hôte, nom de la base et encodage UTF-8
					$this->cfg->DB_USER, // Nom d'utilisateur MySQL
					$this->cfg->DB_PASSWORD, // Mot de passe MySQL
					$options // Options PDO définies précédemment
				);
			}
		} catch (PDOException $e) {
			// En cas d'erreur lors de la connexion, capture l'exception PDOException
			// Prépare un tableau d'erreur avec le message de l'exception
			$error = ['error_msg' => $e->getMessage()];
			// Envoie une réponse JSON avec le message d'erreur et le code HTTP 412 (Precondition Failed)
			$this->response(json_encode($error), 412);
		}
	}
	// Create the MySQL Table
	private function create_table_mysql()
	{
		// SQL statement to create the table if it does not exist
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $this->cfg->DB_TABLE . '` ('
			. ' `ID` bigint(20) NOT NULL,'
			. ' `DateHeureMAJ` datetime NOT NULL default \'0000-00-00 00:00:00\','
			. ' `TitreVF` varchar(255) NOT NULL default \'\','
			. ' `TitreVO` varchar(255) NOT NULL default \'\','
			. ' `Genre` varchar(50) NOT NULL default \'\','
			. ' `Pays` varchar(255) NOT NULL default \'\','
			. ' `Annee` varchar(10) NOT NULL default \'\','
			. ' `Duree` int(11) NOT NULL default \'0\','
			. ' `Note` int(11) NOT NULL default \'0\','
			. ' `Synopsis` text,'
			. ' `Acteurs` text,'
			. ' `Realisateurs` text,'
			. ' `Commentaires` text,'
			. ' `Support` varchar(50) NOT NULL default \'\','
			. ' `NombreSupport` int(11) NOT NULL default \'0\','
			. ' `Edition` varchar(255) NOT NULL default \'\','
			. ' `Zone` varchar(10) NOT NULL default \'\','
			. ' `Langues` varchar(255) NOT NULL default \'\','
			. ' `SousTitres` varchar(255) NOT NULL default \'\','
			. ' `Audio` varchar(255) NOT NULL default \'\','
			. ' `Bonus` text,'
			. ' `EntreeType` varchar(255) NOT NULL default \'\','
			. ' `EntreeSource` varchar(255) NOT NULL default \'\','
			. ' `EntreeDate` date NOT NULL default \'0000-00-00\','
			. ' `EntreePrix` float NOT NULL default \'0\','
			. ' `Sortie` varchar(10) NOT NULL default \'\','
			. ' `SortieType` varchar(255) NOT NULL default \'\','
			. ' `SortieDestinataire` varchar(255) NOT NULL default \'\','
			. ' `SortieDate` date NOT NULL default \'0000-00-00\','
			. ' `SortiePrix` float NOT NULL default \'0\','
			. ' `PretEnCours` varchar(10) NOT NULL default \'\','
			. ' `FilmVu` varchar(5) NOT NULL default \'NON\','
			. ' `Reference` varchar(255) NOT NULL default \'\','
			. ' `BAChemin` varchar(255) NOT NULL default \'\','
			. ' `BAType` varchar(10) NOT NULL default \'\','
			. ' `MediaChemin` varchar(255) NOT NULL default \'\','
			. ' `MediaType` varchar(10) NOT NULL default \'\','
			. ' PRIMARY KEY (`ID`),'
			. ' KEY `TitreVF` (`TitreVF`)'
			. ' ) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;';

		try {
			// Execute the SQL query to create the table
			$this->db->query($sql);
		} catch (Exception $e) {
			// Return error response if table creation fails
			$error = array('error_code' => "200", 'error_msg' => $e->getMessage());
			$this->response(json_encode($error), 424);
		}

		// Return success response if table creation succeeds
		$success = array('status' => "OK");
		$this->response(json_encode($success), 200);
	}

	// Create the SQLite database table and index
	private function create_table_sqlite()
	{
		// SQL statement to create the table with appropriate column types and defaults
		$sql =
			"CREATE TABLE " . $this->cfg->DB_TABLE . " ("
			. "ID integer NOT NULL PRIMARY KEY,"
			. "DateHeureMAJ TimeStamp NOT NULL default '0000-00-00 00:00:00',"
			. "TitreVF varchar(255) NOT NULL default '',"
			. "TitreVO varchar(255) default '',"
			. "Genre varchar(50) default '',"
			. "Pays varchar(255) default '',"
			. "Annee varchar(10) default '',"
			. "Duree int(11) default '0',"
			. "Note int(11) default '0',"
			. "Synopsis text ,"
			. "Acteurs text ,"
			. "Realisateurs text ,"
			. "Commentaires text ,"
			. "Support varchar(50) default '',"
			. "NombreSupport int(11) default '0',"
			. "Edition varchar(255) default '',"
			. "Zone varchar(10) default '',"
			. "Langues varchar(255) default '',"
			. "SousTitres varchar(255) default '',"
			. "Audio varchar(255) default '',"
			. "Bonus text ,"
			. "EntreeType varchar(255) default '',"
			. "EntreeSource varchar(255) default '',"
			. "EntreeDate date default '0000-00-00',"
			. "EntreePrix float default '0',"
			. "Sortie varchar(10) default '',"
			. "SortieType varchar(255) default '',"
			. "SortieDestinataire varchar(255) default '',"
			. "SortieDate date default '0000-00-00',"
			. "SortiePrix float default '0',"
			. "PretEnCours varchar(10) default '',"
			. "FilmVu varchar(5) default 'NON',"
			. "Reference varchar(255) default '',"
			. "BAChemin varchar(255) default '',"
			. "BAType varchar(10) default '',"
			. "MediaChemin varchar(255) default '',"
			. "MediaType varchar(10) default '');"
			// Create an index on the TitreVF column for faster searches
			. "CREATE INDEX films_idx ON " . $this->cfg->DB_TABLE . " (TitreVF ASC);";

		try {
			// Execute the SQL query to create the table and index
			$this->db->query($sql);
			// Return success response if creation succeeds
			$success = array('status' => "OK");
			$this->response(json_encode($success), 200);
		} catch (Exception $e) {
			// On failure, attempt to execute the query again (likely an error)
			$this->db->query($sql);
			// Return failure response
			$success = array('status' => "KO");
			$this->response(json_encode($success), 200);
		}
	}

	// Prepare SQL statement according to db type
	private function sql_escape($field)
	{
		// If using SQLite, escape single quotes by doubling them
		if ($this->cfg->DB_TYPE == 'sqlite') {
			return str_replace('\'', '\'\'', $field);
		} else {
			// For other DB types, use addslashes to escape special characters
			return addslashes($field);
		}
	}

	// Add a record and the poster (if any)
	private function add_record()
	{
		$this->check_code();

		// List of fields to insert into the database
		$champs = array("DateHeureMAJ", "TitreVF", "TitreVO", "Genre", "Pays", "Annee", "Duree", "Note", "Synopsis", "Acteurs", "Realisateurs", "Commentaires", "Support", "NombreSupport", "Edition", "Zone", "Langues", "SousTitres", "Audio", "Bonus", "EntreeType", "EntreeSource", "EntreeDate", "EntreePrix", "Sortie", "SortieType", "SortieDestinataire", "SortieDate", "SortiePrix", "PretEnCours", "FilmVu", "Reference", "BAChemin", "BAType", "MediaChemin", "MediaType");

		// Start building the SQL INSERT statement
		$sql = 'INSERT INTO ' . $this->cfg->DB_TABLE . '(ID';
		foreach ($champs as $value) {
			$sql .= ', ' . $value;
		}

		$sql .= ') VALUES(\'' . $this->_request['ID'] . '\'';

		// Add each field value, properly escaped
		foreach ($champs as $value) {
			$sql .= ', \'' . $this->sql_escape($this->_request[$value]) . '\'';
		}

		$sql .= ");";

		try {
			// Execute the SQL query to add the record
			$data = $this->db->query($sql);
		} catch (Exception $e) {
			// On error, respond with error code and message
			$tableau = array('error_code' => '300', 'error_msg' => $e->getMessage());
			$this->response(json_encode($tableau), 424);
		}
		// Add the poster image if provided
		$this->add_poster();
	}

	// Add a poster image for the record
	private function add_poster()
	{
		$this->check_code();
		$repertoire_affiches = '../' . $this->cfg->POSTERS_DIRECTORY;
		if (isset($this->_request['Affiche'])) {
			// Decode the base64 encoded image data
			$affiche = base64_decode($this->_request['Affiche']);
			// Define the filename for the poster image
			$filename = sprintf($repertoire_affiches . '/Filmotech_%05d.jpg', $this->_request['ID']);
			// Attempt to open the file for writing
			if (!$handle = fopen($filename, 'wb')) {
				// Respond with error if file cannot be opened
				$error = array('error_code' => '301');
				$this->response(json_encode($error), 424);
			}
			// Write the image data to the file
			if (fwrite($handle, $affiche) === FALSE) {
				// Respond with error if writing fails
				$error = array('error_code' => '302');
				$this->response(json_encode($error), 424);
			}
			fclose($handle);
			// Optionally change file permissions if requested
			if (isset($this->_request['forceCHMOD'])) chmod($filename, 0777);
		}
	}

	// Empty the poster directory by deleting all poster files
	private function empty_poster_directory()
	{
		$this->check_code();

		$repertoire_affiches = '../' . $this->cfg->POSTERS_DIRECTORY;
		// Loop through all poster files and delete them
		foreach (glob($repertoire_affiches . '/Filmotech*.jpg') as $filename) {
			unlink($filename);
		}
	}

	// Remove a record and its poster image (if any)
	private function del_record()
	{
		$this->check_code();
		// Build SQL DELETE statement to remove record by ID
		$sql = "DELETE FROM " . $this->cfg->DB_TABLE . " WHERE ID = " . $this->_request['ID'];
		try {
			// Execute the delete query
			$this->db->query($sql);
		} catch (Exception $e) {
			// Respond with error if deletion fails
			$error = array('error_code' => '500', 'error_msg' => $e->getMessage());
			$this->response(json_encode($error), 424);
		}
		// Define the poster filename associated with the record
		$repertoire_affiches = '../' . $this->cfg->POSTERS_DIRECTORY;
		$filename = sprintf($repertoire_affiches . '/Filmotech_%05d.jpg', $this->_request['ID']);
		// Delete the poster file if it exists
		if (file_exists($filename)) {
			unlink($filename);
		}
	}
	// Update the last publishing date (shown in the movie list page)
	private function update_publishing_date()
	{
		$this->check_code();
		$filename = '../update.txt';
		// Try to open the file for writing
		if (!$handle = fopen($filename, 'w')) {
			// Respond with error if file cannot be opened
			$error = array('error_code', '400');
			$this->response(json_encode($error), 424);
		}

		// Write the update date to the file
		if (fwrite($handle, $this->_request['DateMAJ']) === FALSE) {
			// Respond with error if writing fails
			$error = array('error_code', '401');
			$this->response(json_encode($error), 424);
		}
		// Close the file handle
		fclose($handle);
		// Respond with success status
		$success = array('status' => 'OK');
		$this->response(json_encode($success), 200);
	}

	// PUBLIC API FUNCTIONS

	// Check if the service is available
	function check_server()
	{
		// Respond with status OK to indicate server is available
		$success = array('status' => "OK");
		$this->response(json_encode($success), 200);
	}

	// Get the configuration of the API and some parameters
	protected function get_config()
	{
		$this->check_code();

		// Prepare configuration data to send
		$tableau = array('status' => 'OK');
		$tableau["API_VERSION"] = $this->cfg->API_VERSION;
		$tableau["POSTERS_DIRECTORY"] = $this->cfg->POSTERS_DIRECTORY;
		$tableau["DB_TABLE"] = $this->cfg->DB_TABLE;
		$tableau["PHP_VERSION"] = PHP_VERSION;
		// Send the configuration data as JSON response
		$this->response(json_encode($tableau), 200);
	}

	// Create the poster directory if it does not exist
	function create_poster_directory()
	{
		$this->check_code();

		$result = false;
		$repertoire_affiches = '../' . $this->cfg->POSTERS_DIRECTORY;
		$forceChmod = $this->_request['forceCHMOD'];

		// Check if the directory exists
		if (!is_dir($repertoire_affiches)) {
			// Attempt to create the directory
			$result = mkdir($repertoire_affiches);
			if (!$result) {
				// Respond with error if creation fails
				$error = array('error_code' => '201');
				$this->response(json_encode($error), 424);
			}
		}

		// If forceCHMOD is set to 'OUI', change permissions to 0777
		if ($forceChmod == 'OUI') {
			chmod($repertoire_affiches, 0777);
			// Respond with success and message about forced chmod
			$success = array('status' => 'OK', 'message' => 'CHMOD forcé');
			$this->response(json_encode($success), 200);
		}

		// Respond with success status
		$success = array('status' => 'OK');
		$this->response(json_encode($success), 200);
	}

	// Create the table in the database depending on DB type
	private function create_table()
	{
		$this->check_code();

		// Call the appropriate method based on database type
		if ($this->cfg->DB_TYPE == 'sqlite') {
			$this->create_table_sqlite();
		} else {
			$this->create_table_mysql();
		}
	}

	// Return ID/Update date from the database
	private function get_movie_list()
	{
		$this->check_code();
		// Initialize response array with status OK
		$tableau = array('status' => 'OK');
		// Query database for ID and update date
		$res = $this->db->query("SELECT ID, DateHeureMAJ FROM " . $this->cfg->DB_TABLE);
		// Populate response array with ID as key and update date as value
		foreach ($res as $row) {
			$tableau[$row['ID']] = $row['DateHeureMAJ'];
		}
		// Send JSON response with the data
		$this->response(json_encode($tableau), 200);
	}

	// Main process to add, update or remove records based on ACTION parameter
	private function publish()
	{
		$this->check_code();

		// If ForceUpdate is set, clear the poster directory
		if (!empty($this->_request['ForceUpdate'])) {
			$this->empty_poster_directory();
		}

		// Determine action to perform
		switch ($this->_request['ACTION'] ?? '') {
			case 'ADD':
				// Add a new record
				$this->add_record();
				break;
			case 'UPDATE':
				// Delete existing record then add updated record
				$this->del_record();
				$this->add_record();
				break;
			case 'DELETE':
				// Delete the specified record
				$this->del_record();
				break;
		}

		// Prepare response with action details
		$tableau = [
			"action" => $this->_request['ACTION'] ?? null,
			"TitreVF" => $this->_request['TitreVF'] ?? null,
			"ID" => $this->_request['ID'] ?? null,
		];
		// Send JSON response confirming the action
		$this->response(json_encode($tableau), 200);
	}

	// MAIN PROCESS

	public function processApi()
	{
		// Retrieve the requested API function from the request parameters
		$rquest = $_REQUEST['rquest'] ?? null;

		if ($rquest !== null) {
			// Clean and format the requested function name
			$func = strtolower(trim(str_replace("/", "", $rquest)));
		} else {
			// Respond with error if no request specified
			$this->response('Invalid request', 400);
			return;
		}

		// Check if the requested function is a valid service
		if (in_array($func, $this->services)) {
			// Call the requested function
			$this->$func();
		} else {
			// Respond with 404 if function not found
			$this->response('', 404);
		}
	}
}

$api = new API();
// Start processing the API request
$api->processApi();

ob_end_flush();
?>