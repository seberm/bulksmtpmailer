<?php
/** Main MySQL class for connection to given MySQL server
 * @author Otto Sabart <seberm@gmail.com>
 */


class MySQLi {
	
	/** The database server
	 * @var string $server
	 */
	private $server;
	
	/** Database username
	 * @var string $username
	 */
	private $username;
	
	/** Database password
	 * @var string $password
	 */
	private $password;
	
	/** Database name
	 * @var string $database
	 */
	private $database;
	
	/** The database connection indentifer (ID)
	 * @var int $connection
	 */
	private $connection;
	
	/** MySQL connection error
	 * @var string $conError
	 */
	public $connect_error = "";
	
	/** MySQL connection errno
	 * @var string $conErrno
	 */
	public $connect_errno = 0;
	
	/** MySQL query error
	 * @var string $error
	 */
	public $error = "";
	
	/** MySQL query errno
	 * @var string $errno
	 */
	public $errno = 0;
	
	/** Inserted row ID
	 * @var int $insertId
	 */
	public $insert_id = -1;
	
	
	/** Constructor - creates new MySQL instance
	 * @param string $server
	 * @param string $username
	 * @param string $password
	 * @param string $database
	 */
	function MySQLi ($server, $username, $password, $database) {
		
		$this->server = $server;
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;
		
		$this->connection = Mysql_Connect($this->server
										  $this->username,
										  $this->password);
	
		if (!$this->connection) {
			
			$this->connect_error = MySQL_Error();
			$this->connect_errno = MySQL_Errno();
		}
	}

	
	/** Function closes current database connection
	 * @return bool
	 */
	public function close () {
		
		return MySQL_Close($this->connection);
	}
	
	
	/** Runs given SQL request
	 * @param string $sql
	 * @return MySQLiResult
	 */
	public function query ($sql) {
		
		$res = MySQL_Query($sql, $this->connection);
		
		if (!$res) {
			
			$this->error = MySQL_Error($this->connection);
			$this->errno = MySQL_Errno($this->connection);
			
			return false;
		} else {
			
			$result = new MySQLi_Result ($res, $this->connection);
			$this->insertId = mysql_insert_id($this->connection);
			
			return $result;
		}
	}
}


class MySQLi_Result {
	
	private $result;
	private $connection;
	public $num_rows;


	/** Creates new instance of MySQL result with given connection
	 *
	 * @param Resource $result
	 * @param Mysql link $connection
	 */
	function MySQLi_Result ($result, $connection) {
		
		$this->result = $result;
		$this->connection = $connection;
	
		$this->num_rows = MySQL_Num_Rows($this->connection);
	}
	
	
	/** Returns fetched result with associated keys
	 * @return array
	 */
	public function fetch_assoc () {
		
		return MySQL_Fetch_Assoc($this->result, $this->connection);
	}
	
	
	/** Returns fetched result -> row
	 * @return array
	 */
	public function fetch_row () {
		
		return MySQL_Fetch_Row($this->result, $this->connection);
	}
	
	
	/** Returns fetched result with numeric keys
	 * @return array
	 */
	public function fetch_array () {
		
		return MySQL_Fetch_Array($this->result, $this->connection);
	}
}





function mysqli_connect($server, $username, $password, $database) {
	
    return new MySQLi($server, $username, $password, $database);
}


/** Perform MySQL query on given MySQLi object
 *
 * @see MySQLi::query()
 * @param MySQLi $link
 * @param string $query
 * @return MySQLi_result
 */
function mysqli_query($link, $query) {
	
    return $link->query($query);
}


/** Close the connection of the given object
 *
 * @see MySQLi::close()
 * @param MySQLi $link
 * @return bool
 */
function mysqli_close($link) {
	
    return $link->close();
}


/** Returns number of rows in given MySQLi_result object
 *
 * @see MySQLi_result::num_rows
 * @param MySQLi_result $result
 * @return int Returs number of rows
 */
function mysqli_num_rows($result) {
	
    return $result->num_rows;
}


/** Returns associative array with results
 *
 * @see MySQLi_result::fetch_assoc()
 * @param MySQLi_result $result
 * @return array
 */
function mysqli_fetch_assoc($result) {
	
    return $result->fetch_assoc();
}


/** Returns numberindexed array with results
 *
 * @see MySQLi_result::fetch_row()
 * @param MySQLi_result $result
 * @return array
 */
function mysqli_fetch_row($result) {
	
    return $result->fetch_row();
}


/** Returns both, associative and number-indexed array with results
 *
 * @see MySQLi_result::fetch_array()
 * @param MySQLi_result $result
 * @return array
 */
function mysqli_fetch_array($result) {
	
    return $result->fetch_array();
}


/** Returns last inserted ID 
 * 
 * @param Resource $link
 * @return int
 */
function mysql_insert_id($link) {
	
    return $link->insert_id;
}


?> 
