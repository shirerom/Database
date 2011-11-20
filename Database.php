<?php

/**
 * Version 2011-02-15
 */


class DatabaseException extends Exception {}

/**
 *
 */
class Database extends mysqli {


	/**
	 * Konstruktor
	 *
	 * @param string $hostname
	 * @param string $username
	 * @param string $password
	 * @param string $database
	 */
	public function __construct($hostname, $username, $password = "", $database = "") {
		
		parent::__construct($hostname, $username, $password, $database);

		$this->query("set autocommit=1");
	}

	/**
	 * beginnt eine Transaktion
	 */
	public function begin() {

		$this->query("set autocommit=0");
		$this->query("START TRANSACTION");
	}

	/**
	 * beendet eine Transaktion erfolgreich
	 */
	public function commit() {

		$this->query("COMMIT");
		$this->query("set autocommit=1");
	}

	/**
	 * beendet eine Transaktion erfolglos
	 */
	public function rollback() {

		$this->query("ROLLBACK");
		$this->query("set autocommit=1");
	}

	/**
	 * führt eine Datenbankabfrage aus
	 *
	 * @param string $query
	 * @return mixed
	 *
	 * @throws DatabaseException
	 */
	public function execute($query) {

		$value = parent::query($query);

		// boolscher Rückgabewert
		// → INSERT, UPDATE, DELETE
		if(is_bool($value) and $value === true) {
			return $value;
		}

		// Object als Rückgabewert
		// → SELECT, SHOW, EXPLAIN
		elseif(is_object($value)){
			return new DatabaseResult($value);
		}

		else {
			throw new DatabaseException($this->error, $this->errno);
		}

	}

	/**
	 * schließt eine Datenbankverbindung
	 */
	public function close() {
		parent::close();
	}

	/**
	 * 
	 * @return int
	 */
	public function getInsertId() {
		return $this->insert_id;
	}
	
}


class DatabaseResult {


	protected $result;


	public function __construct(mysqli_result $result) {

		$this->result = $result;

	}


	public function getRow() {

		return $this->result->fetch_assoc();

	}


	public function getRowsAll() {

		$rows = array();

		while($row = $this->result->fetch_assoc())  {
			$rows[] = $row;
		}

		return $rows;

	}
	
}
