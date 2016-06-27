<?php
require_once 'class.database.php';
require_once 'class.user.php';

class AccessControl
{
	private $object_level;

	function __construct($item){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM permission_item WHERE item = :item";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':item', $item);
			$stmt->execute();
			if($stmt->rowCount() == 1){
				$item = $stmt->fetch(PDO::FETCH_OBJ);
				$this->object_level = $item->level;
			}else{
				$this->object_level = 5;
			}
		} catch(PDOException $e) {
			throw new Execption('AccessControl: Init: ' . $e->getMessage());
		}
	}

	public function getPermission(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM permission WHERE level = :level";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':level', $this->object_level);
			$stmt->execute();
			// return $stmt->fetchAll();
			if($stmt->rowCount() == 1){
				$item = $stmt->fetch(PDO::FETCH_OBJ);
				return $item;
			}else{
				return null;
			}
		} catch(PDOException $e) {
			throw new Execption('AccessControl: GetPermission: ' . $e->getMessage());
		}
	}

	public function hasReadAccess($user){
		return ($this->object_level <= $user->get_permission()->level);
	}

	public static function checkIP($ip){
		return true;
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM access_control WHERE ip = :ip";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':ip', $ip);
			$stmt->execute();
			if($stmt->rowCount() != 1) return false;
			return true;
		} catch(PDOException $e) {
			throw new Exception('AccessControl: CheckIP: '.$e->getMessage());
		}
	}

	public static function addIP($ip){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "INSERT INTO access_control(ip) VALUES (:ip)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':ip', $ip);
			$stmt->execute();
		} catch(PDOException $e) {
			throw new Exception('AccessControl: AddIP: '.$e->getMessage());
		}
	}

	public static function deleteIP($id){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "DELETE FROM access_control WHERE id = :id";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':id', $id);
			$stmt->execute();
		} catch(PDOException $e) {
			throw new Exception('AccessControl: DeleteIP: '.$e->getMessage());
		}
	}

	public static function getIPs(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM access_control";
			$stmt = $dbo->prepare($sql);
			$stmt->execute();
			return $stmt->fetchAll();
		} catch(PDOException $e) {
			throw new Exception('AccessControl: GetIPs: '.$e->getMessage());
		}
	}
}