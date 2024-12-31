<?php

	/*
			Filmotech publishing API
			(c) 2013-2023 by Pascal PLUCHON
			https://www.filmotech.fr
 	*/

	require_once("../include/config.inc.php");

    class API {

		public $data = "";
		private $cfg;
		private $db = NULL;

		public $_allow = array();
		public $_content_type = "application/json";
		public $_request = array();

        private $_method = "";
		private $_code = 200;

        // List of allowed methods
		private $services = array("check_server", "check_code", "get_config", "create_poster_directory",
        "get_movie_list", "create_table", "update_publishing_date", "publish" );

		// Constructor
		public function __construct(){
			$this->inputs();					// Clean up input data
			$this->cfg = new CONFIG(); 			// Init database parameters
			$this->dbConnect();					// Initiate Database connection
		}

		// Return status code message
        public function get_status_message(){
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
						505 => 'HTTP Version Not Supported');
			return ($status[$this->_code])?$status[$this->_code]:$status[500];
		}

		// Clean up input
		private function inputs(){
			switch($this->get_request_method()){
				case "POST":
					$data = json_decode(file_get_contents('php://input'), true);
					$this->_request = $this->cleanInputs($data);
					break;
				case "GET":
					$this->_request = $this->cleanInputs($_GET);
					break;
				case "DELETE":
					$this->_request = $this->cleanInputs($_GET);
					break;
				case "PUT":
					parse_str(file_get_contents("php://input"),$this->_request);
					$this->_request = $this->cleanInputs($this->_request);
					break;
				default:
					$this->response('',406);
					break;
			}
		}

		private function cleanInputs($data){
			$clean_input = array();

			if(is_array($data)){
				foreach($data as $k => $v){
					$clean_input[$k] = $this->cleanInputs($v);
				}
			} else {
				$data = strip_tags($data);
				$clean_input = trim($data);
			}

			return $clean_input;
		}

		// Check request method
		public function get_request_method(){
			return $_SERVER['REQUEST_METHOD'];
		}

		// Return a JSON response and a HTTP status code
        public function response($data,$status){
            $this->_code = ($status)?$status:200;
			$this->set_headers();
			echo $data;
			exit;
		}

		// Set the header for the response
        private function set_headers(){
			header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
			header("Content-Type:".$this->_content_type.";charset=utf-8");
		}

		// Check the security code (API_ACCESS_CODE) and the access method (POST)
		function check_code(){

			if($this->get_request_method() != "POST")
			{
				$error = array('error_code' => "100" );
				$this->response(json_encode($error),401);
			}

			if (!$this->_request['code']) {
				$error = array('error_code' => "101" );
				$this->response(json_encode($error),401);
			}

			$code = $this->_request['code'];
			if ($code!=$this->cfg->API_ACCESS_CODE) {
				$error = array('error_code' => "102" );
				$this->response(json_encode($error),401);
			}
		}

		// Database connection
		private function dbConnect(){
			error_reporting(E_ALL); // Disable this to see PHP errors
			try
			{
				if ( $this->cfg->DB_TYPE == 'sqlite' ) {
					$db_init = new PDO('sqlite:../'.$this->cfg->DB_NAME.'.sqlite3');
					// $db_init->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Enable this to see PDO errors
				} else {
					$db_init = new PDO('mysql:host='.$this->cfg->DB_SERVER.';dbname='.$this->cfg->DB_NAME,
					$this->cfg->DB_USER, $this->cfg->DB_PASSWORD);
					$db_init->query("SET NAMES UTF8");
				}
			}
			catch (Exception $e)
			{
				$error = array( 'error_msg' => $e->getMessage() );
				$this->response(json_encode($error), 412);
			}
			$this->db = $db_init;
		}

		// Create the MySQL Table
		private function create_table_mysql() {
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

			try
			{
				$this->db->query($sql);
			}
			catch (Exception $e)
			{
				$error = array('error_code' => "200" , 'error_msg' => $e->getMessage() );
				$this->response(json_encode($error),424);
			}

			$success = array('status' => "OK" );
			$this->response(json_encode($success),200);
		}

		// Create the SQLite database
		private function create_table_sqlite() {
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
	        . "CREATE INDEX films_idx ON " . $this->cfg->DB_TABLE . " (TitreVF ASC);";

			try
			{
				$this->db->query($sql);
				$success = array('status' => "OK" );
				$this->response($this->json($success),200);
			}
			catch (Exception $e)
			{
				$this->db->query($sql);
				$success = array('status' => "KO" );
				$this->response($this->json($success),200);
			}

		}

		// Prepare SQL statement according to db type
		private function sql_escape($field) {
			if ( $this->cfg->DB_TYPE == 'sqlite' )
				{ return str_replace('\'','\'\'',$field); }
			else
				{ return addslashes($field); }

		}

		// Add a record and the poster (if any)
		private function add_record() {
			$this->check_code();

			$champs = array( "DateHeureMAJ", "TitreVF", "TitreVO", "Genre", "Pays", "Annee", "Duree", "Note", "Synopsis", "Acteurs", "Realisateurs", "Commentaires", "Support", "NombreSupport", "Edition", "Zone", "Langues", "SousTitres", "Audio", "Bonus", "EntreeType", "EntreeSource", "EntreeDate", "EntreePrix", "Sortie", "SortieType", "SortieDestinataire", "SortieDate", "SortiePrix", "PretEnCours", "FilmVu", "Reference", "BAChemin", "BAType", "MediaChemin", "MediaType" );

			$sql = 'INSERT INTO ' . $this->cfg->DB_TABLE . '(ID';
			foreach ($champs as $value) {
				$sql .= ', ' . $value;
			}

			$sql .= ') VALUES(\''.$this->_request['ID'].'\'';

			foreach ($champs as $value) {
				$sql .= ', \'' . $this->sql_escape($this->_request[$value]) . '\'';
			}

			$sql .= ");";

			try {
				$data = $this->db->query($sql);
			}
			catch (Exception $e)
			{
				$tableau = array('error_code' => '300' , 'error_msg' => $e->getMessage() );
				$this->response(json_encode($tableau),424);
			}
 			$this->add_poster();
		}

		// Add a poster
		private function add_poster() {
			$this->check_code();
			$repertoire_affiches = '../' . $this->cfg->POSTERS_DIRECTORY;
			if (isset($this->_request['Affiche'])) {
				$affiche = base64_decode($this->_request['Affiche']);
				$filename = sprintf($repertoire_affiches.'/Filmotech_%05d.jpg' , $this->_request['ID'] );
				if (!$handle = fopen($filename, 'wb')) {
					$error = array( 'error_code' => '301' );
					$this->response(json_encode($error),424);
				}
				if (fwrite($handle, $affiche) === FALSE) {
					$error = array( 'error_code' => '302' );
					$this->response(json_encode($error),424);
				}
				fclose($handle);
				if (isset($this->_request['forceCHMOD'])) chmod( $filename , 0777 );
			}
		}

		// Empty poster directory
		private function empty_poster_directory(){
			$this->check_code();

			$repertoire_affiches = '../' . $this->cfg->POSTERS_DIRECTORY;
			foreach (glob($repertoire_affiches.'/Filmotech*.jpg') as $filename) {
				unlink($filename);
			}
		}

		// Remove a record and his poster (if any)
		private function del_record() {
			$this->check_code();
			$sql = "DELETE FROM " . $this->cfg->DB_TABLE . " WHERE ID = " . $this->_request['ID'];
			try {
				$this->db->query($sql);
			} catch (Exception $e) {
				$error = array('error_code' => '500' , 'error_msg' => $e->getMessage()  );
				$this->response(json_encode($error),424);
			}
			$repertoire_affiches = '../' . $this->cfg->POSTERS_DIRECTORY;
			$filename = sprintf($repertoire_affiches.'/Filmotech_%05d.jpg' , $this->_request['ID'] );
			if (file_exists($filename)) { unlink($filename); }
		}

		// Update the last publishing date (shown in the movie list page)
		private function update_publishing_date(){
			$this->check_code();
			$filename = '../update.txt';
			if (!$handle = fopen($filename, 'w')) {
				$error = array('error_code' , '400' );
				$this->response(json_encode($$error),424);
			}

			if (fwrite($handle, $this->_request['DateMAJ'] ) === FALSE) {
				$error = array('error_code' , '401' );
				$this->response(json_encode($error),424);
			}
			fclose($handle);
			$success = array('status' => 'OK' );
			$this->response(json_encode($success),200);
		}

		// PUBLIC API FUNCTIONS

        // Check if the service is available
		function check_server(){
			$success = array('status' => "OK" );
            $this->response(json_encode($success),200);
		}

		// Get the configuration of the API and some parameters
		protected function get_config(){
			$this->check_code();

			$tableau = array('status' => 'OK' );
			$tableau["API_VERSION"] = $this->cfg->API_VERSION;
			$tableau["POSTERS_DIRECTORY"] = $this->cfg->POSTERS_DIRECTORY;
			$tableau["DB_TABLE"] = $this->cfg->DB_TABLE;
			$tableau["PHP_VERSION"] = PHP_VERSION;
			$this->response(json_encode($tableau),200);
		}

		function create_poster_directory() {
			$this->check_code();

			$result = false;
			$repertoire_affiches = '../' . $this->cfg->POSTERS_DIRECTORY;
			$forceChmod = $this->_request['forceCHMOD'];

			if (!is_dir($repertoire_affiches)) {
				$result = mkdir($repertoire_affiches);
				if (!$result)
				{
					$error = array( 'error_code' => '201'  );
					$this->response(json_encode($error),424);
				}
			}

			if ($forceChmod == 'OUI') {
				chmod($repertoire_affiches, 0777);
				$success = array('status' => 'OK', 'message' => 'CHMOD forcé');
				$this->response(json_encode($success),200);
			}

			$success = array('status' => 'OK' );
			$this->response(json_encode($success),200);
		}

		// Create the table in the database
		private function create_table() {
			$this->check_code();

			if ( $this->cfg->DB_TYPE == 'sqlite' ) {
				$this->create_table_sqlite();
			} else {
				$this->create_table_mysql();
			}
		}

		// Return ID/Update date from the database
		private function get_movie_list(){
			$this->check_code();
			$tableau = array('status' => 'OK' );
			$res = $this->db->query("SELECT ID, DateHeureMAJ FROM " . $this->cfg->DB_TABLE );
			foreach ($res as $row) {
			    $tableau[$row['ID']] = $row['DateHeureMAJ'];
			}
			$this->response(json_encode($tableau),200);
		}

		// Main processs, add, update or remove records
		private function publish(){
			$this->check_code();

			if (isset($this->_request['ForceUpdate'])) $this->empty_poster_directory();

			if ($this->_request['ACTION']=='ADD') {
				$this->add_record();
			}

			if ($this->_request['ACTION']=='UPDATE') {
				$this->del_record();
				$this->add_record();
			}

			if ($this->_request['ACTION']=='DELETE') {
				$this->del_record();
			}

			$tableau = array("action" => $this->_request['ACTION'] ,
				"TitreVF" => $this->_request['TitreVF'] , "ID" => $this->_request['ID'] );
			$this->response(json_encode($tableau),200);
		}


		// MAIN PROCESS

        public function processApi(){
            $func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
            if (in_array($func,$this->services))
                $this->$func();
            else
                $this->response('',404);
        }

    }

    $api = new API();
    $api->processApi();
?>
