<?php

class DATABASE
{
	const DBHOST = 'localhost';
	const DBUSER = 'ivyleague';
	const DBPASS = '5:1etMiyTrfdmxNNbqyfwisiyxuNeSaaxZaXjhxLfrSD@747';
	const DBNAME = 'logining';

	protected $dbo = null;

	function __construct() {
		try{
			$this->dbo = new PDO('mysql:host='.self::DBHOST.';dbname='.self::DBNAME.';charset=utf8', self::DBUSER, self::DBPASS);
		} catch(PDOException $e) {
			error_log("Class::DATABASE : " . $e->getMessage());
			die();
		}
	}

	function getDBO() {
		return $this->dbo;
	}

	function __destruct() {
		$this->dbo = null;
	}

}