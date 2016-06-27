<?php

require_once 'class.database.php';

class User
{
	private $user;
	private $permission;

	function __construct($uid) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$sql = "SELECT * FROM user WHERE id = :uid";
		$sql1 = "SELECT * FROM permission WHERE level = :level";
		try{
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $uid);
			$stmt->execute();
			if($stmt->rowCount() != 1) throw new Exception('User: user not exist.');
			$this->user = $stmt->fetch(PDO::FETCH_OBJ);
			$stmt = $dbo->prepare($sql1);
			$stmt->bindParam(':level', $this->user->level);
			$stmt->execute();
			$this->permission = $stmt->fetch(PDO::FETCH_OBJ);
		} catch(PDOException $e) {
			throw new Execption('User: DB: ' . $e->getMessage());
		}
	}

	public function get_profile() {
		return $this->user;
	}

	public function get_permission() {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM permission WHERE level = :level";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':level', $this->permission->level);
			$stmt->execute();
			// return $stmt->fetchAll();
			if($stmt->rowCount() == 1){
				$item = $stmt->fetch(PDO::FETCH_OBJ);
				return $item;
			}else{
				return null;
			}
		} catch(PDOException $e) {
			throw new Execption('User: GetPermission: ' . $e->getMessage());
		}
	}

	public function get_parent() {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM user WHERE id = (SELECT parents FROM user WHERE id = :id);";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':id', $this->user->id);
			$stmt->execute();
			// return $stmt->fetchAll();
			if($stmt->rowCount() == 1){
				$item = $stmt->fetch(PDO::FETCH_OBJ);
				return $item;
			}else{
				return null;
			}
		} catch(PDOException $e) {
			throw new Execption('User: GetParent: ' . $e->getMessage());
		}
	}

	public function get_children() {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM user WHERE parents = (SELECT id FROM user WHERE id = :id)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':id', $this->user->id);
			$stmt->execute();
			// return $stmt->fetchAll();
			if($stmt->rowCount() == 1){
				$item = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $item;
			}else{
				return null;
			}
		} catch(PDOException $e) {
			throw new Execption('User: GetParent: ' . $e->getMessage());
		}
	}

	public function delete(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "DELETE FROM user_subject WHERE uid = :id";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':id', $this->user->id);
			$stmt->execute();
			$sql = "DELETE FROM user WHERE id = :id";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':id', $this->user->id);
			$stmt->execute();
			$dbo->commit();
			return true;
		} catch(PDOException $e) {
			$dbo->rollback();
			// return false;
			throw new Exception('User: DeleteUser: '.$e->getMessage());
			return false;
		}
	}

	public function getTotalAward(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT sum(score) FROM award WHERE uid = :uid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $this->user->id);
			$stmt->execute();
			$result = null;
			if($stmt->rowCount() == 1)
				$result = $stmt->fetch()[0];
			return $result;
		} catch(PDOException $e) {
			throw new Exception('User: getTotalAward: '.$e->getMessage());
		}
	}

	public function getWeeklyAward(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT sum(score) FROM award WHERE uid = :uid AND WEEKOFYEAR(`timestamp`)=WEEKOFYEAR(NOW())";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $this->user->id);
			$stmt->execute();
			$result = null;
			if($stmt->rowCount() == 1)
				$result = $stmt->fetch()[0];
			return $result;
		} catch(PDOException $e) {
			throw new Exception('User: getWeeklyAward: '.$e->getMessage());
		}
	}

	public static function addAdmin($username, $password, $sex, $birthdate, $telephone, $address, $email, $level, $subject, $name){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO `logining`.`user` (`username`, `password`, `sex`, `borndate`, `telephone`, `address`, `email`, `level`, `name`) VALUES (?,?,?,?,?,?,?,?,?)";
			$stmt = $dbo->prepare($sql);
			$stmt->execute(array($username, $password, $sex, $birthdate, $telephone, $address, $email, $level, $name));
			$id = $dbo->lastInsertId();
			$sql = "INSERT INTO user_subject(uid,sid) VALUES (?, ?)";
			$stmt = $dbo->prepare($sql);
			foreach ($subject as $s) {
				$stmt->execute(array($id, $s));
			}
			$dbo->commit();
			return 1;
		} catch(PDOException $e) {
			$dbo->rollback();
			return -1;
			throw new Exception('User: AddAdmin: '.$e->getMessage());
		}
	}

	public static function addUser($username, $password, $sex, $birthdate, $grade, $telephone, $address, $parents, $email, $level, $subject, $name){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$parent = self::getParent($parents);
			if(!$parent) return 5;
			$sql = "INSERT INTO `logining`.`user` (`username`, `password`, `sex`, `borndate`, `grade`, `telephone`, `address`, `parents`, `email`, `level`, `name`) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
			$stmt = $dbo->prepare($sql);
			$stmt->execute(array($username, $password, $sex, $birthdate, $grade, $telephone, $address, $parent->id, $email, $level, $name));
			$id = $dbo->lastInsertId();
			$sql = "INSERT INTO user_subject(uid,sid) VALUES (?, ?)";
			$stmt = $dbo->prepare($sql);
			foreach ($subject as $s) {
				$stmt->execute(array($id, $s));
			}
			$dbo->commit();
			return 1;
		} catch(PDOException $e) {
			$dbo->rollback();
			return -1;
			throw new Exception('User: AddUser: '.$e->getMessage());
		}
	}

	public static function getParent($username){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM user WHERE level = 1 AND username = :username";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':username', $username);
			$stmt->execute();
			if($stmt->rowCount() > 0)
				return $stmt->fetch(PDO::FETCH_OBJ);
			return null;
		} catch(PDOException $e) {
			throw new Execption('User: GetSubject: ' . $e->getMessage());
		}
	}

	public static function addParent($username, $password, $sex, $birthdate, $telephone, $address, $email, $name){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO `logining`.`user` (`username`, `password`, `sex`, `borndate`, `telephone`, `address`, `email`, `level`, `name`) VALUES (?,?,?,?,?,?,?,'1',?)";
			$stmt = $dbo->prepare($sql);
			$stmt->execute(array($username, $password, $sex, $birthdate, $telephone, $address, $email, $name));
			$dbo->commit();
			return true;
		} catch(PDOException $e) {
			$dbo->rollback();
			// return false;
			throw new Exception('User: AddParent: '.$e->getMessage());
		}
	}

	public function get_subject() {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT s.id, s.name FROM user_subject us LEFT JOIN subject s ON us.sid = s.id WHERE us.uid = :uid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $this->user->id);
			$stmt->execute();
			return $stmt->fetchAll();
		} catch(PDOException $e) {
			throw new Execption('User: GetSubject: ' . $e->getMessage());
		}
	}

	public function get_grade() {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT grade FROM user WHERE id = :uid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $this->user->id);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_OBJ);
		} catch(PDOException $e) {
			throw new Execption('User: GetGrade: ' . $e->getMessage());
		}
	}

	public function get_attempt($qsid) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM attempt WHERE uid = :uid AND qsid = :qsid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $this->user->id);
			$stmt->bindParam(':qsid', $qsid);
			$stmt->execute();
			if($stmt->rowCount() > 0)
				return $stmt->fetch(PDO::FETCH_OBJ);
			else
				return null;
		} catch(PDOException $e) {
			throw new Execption('User: GetGrade: ' . $e->getMessage());
		}
	}

	public function get_calendar_questionset($date) {
		$oneday = 86400;
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT c.lid, c.qsid, c.description, qs.name qsname, qs.description, s.name, s.id, qs.grade FROM calendar c LEFT JOIN question_set qs ON c.qsid = qs.qsid LEFT JOIN subject s ON s.id = qs.subject WHERE c.uid = :uid AND c.timestamp >= FROM_UNIXTIME(:ts) AND c.timestamp < FROM_UNIXTIME(:ts1)";
			$stmt = $dbo->prepare($sql);
			$fwddate = $date + $oneday;
			$stmt->bindParam(':uid', $this->user->id);
			$stmt->bindParam(':ts', $date);
			$stmt->bindParam(':ts1', $fwddate);
			$stmt->execute();
			if($stmt->rowCount() > 0)
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			else
				return null;
		} catch(PDOException $e) {
			throw new Execption('User: GetGrade: ' . $e->getMessage());
		}
	}

	public static function add_subject($name) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO `logining`.`subject` (`name`) VALUES (?)";
			$stmt = $dbo->prepare($sql);
			$stmt->execute(array($name));
			$dbo->commit();
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('User: AddSubject: '.$e->getMessage());
		}
	}

	public static function remove_subject($sid) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "DELETE FROM `subject` WHERE id = :sid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':sid', $sid);
			$stmt->execute();
			$dbo->commit();
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('User: RemoveSubject: '.$e->getMessage());
		}
	}

	public static function get_subjects() {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$sql = "SELECT * FROM subject";
		$stmt = $dbo->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public static function get_permission_by_level($level) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$sql = "SELECT * FROM permission WHERE level = :level";
		$stmt = $dbo->prepare($sql);
		$stmt->bindParam(':level', $level);
		$stmt->execute();
		if($stmt->rowCount() == 1){
			return $stmt->fetch(PDO::FETCH_OBJ);
		}else{
			return null;
		}
	}

	public static function get_permission_list() {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$sql = "SELECT * FROM permission";
		$stmt = $dbo->query($sql);
		if($stmt->rowCount() > 0){
			return $stmt->fetchAll();
		}else{
			return null;
		}
	}

	public static function get_user_by_username($username) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT id FROM user WHERE username = :username";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':username', $username);
			$stmt->execute();
			// return $stmt->fetchAll();
			if($stmt->rowCount() == 1){
				$item = $stmt->fetch(PDO::FETCH_OBJ);
				return new User($item->id);
				// return $item;
			}else{
				return null;
			}
		} catch(PDOException $e) {
			throw new Execption('User: get_user_by_username: ' . $e->getMessage());
		}
	}

	public static function authenticate($username, $pw) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT id,username,password from user where username = :username and password = :password";
			$stmt = $dbo->prepare($sql);
			$password = hash('sha256',$pw);
			$stmt->bindParam(':username', $username);
			$stmt->bindParam(':password', $password);
			$stmt->execute();
			// return $stmt->fetchAll();
			if($stmt->rowCount() == 1){
				$item = $stmt->fetch(PDO::FETCH_ASSOC);
				return $item;
				// return $item;
			}else{
				return null;
			}
		} catch(PDOException $e) {
			throw new Execption('User: Authenticate: ' . $e->getMessage());
		}
	}

}