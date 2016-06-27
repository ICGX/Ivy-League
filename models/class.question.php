<?php

require_once 'class.database.php';

abstract class Question
{
	protected $object;

	public function __construct($qid){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM question WHERE qid = :qid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $qid);
			$stmt->execute();
			if($stmt->rowCount() != 1) throw new Exception('Question '.$qid.': Not exist');
			$this->object = $stmt->fetch(PDO::FETCH_OBJ);
		} catch(PDOException $e) {
			throw new Exception('Question: DB: '.$e->getMessage());
		}
	}

	public function get_id(){
		return $this->object->qid;
	}

	public function get_name(){
		return $this->object->name;
	}

	public function get_type(){
		return $this->object->type;
	}

	public function remove($qsid){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM question_set_relation WHERE qid = :qid AND qsid != :qsid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->bindParam(':qsid', $qsid);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				$sql = "DELETE FROM question_set_relation WHERE qid = :qid AND qsid = :qsid";
				$stmt = $dbo->prepare($sql);
				$stmt->bindParam(':qid', $this->get_id());
				$stmt->bindParam(':qsid', $qsid);
				$stmt->execute();
			}else{
				$sql = "DELETE FROM question_set_relation WHERE qid = :qid";
				$stmt = $dbo->prepare($sql);
				$stmt->bindParam(':qid', $this->get_id());
				$stmt->execute();
				$sql = "DELETE FROM answer WHERE qid = :qid";
				$stmt = $dbo->prepare($sql);
				$stmt->bindParam(':qid', $this->get_id());
				$stmt->execute();
				$sql = "SELECT * FROM image WHERE relation = 'Question' AND relation_id = ?";
				$stmt = $dbo->prepare($sql);
				$stmt->execute(array($this->get_id()));
				foreach ($stmt->fetchAll() as $image) {
					unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/'.$image['id'].'.'.$image['ext']);
				}
				$sql = "DELETE FROM image WHERE relation = 'Question' AND relation_id = :qid";
				$stmt = $dbo->prepare($sql);
				$stmt->bindParam(':qid', $this->get_id());
				$stmt->execute();
				$sql = "DELETE FROM question WHERE qid = :qid";
				$stmt = $dbo->prepare($sql);
				$stmt->bindParam(':qid', $this->get_id());
				$stmt->execute();
			}
			$dbo->commit();
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: DB: DeleteAnswer: '.$e->getMessage());
		}
	}

	public function checkReattempt($uid) {
		date_default_timezone_set('Asia/Hong_Kong');
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM attempt_history WHERE qid = :qid AND uid = :uid ORDER BY timestamp DESC";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(":qid", $this->get_id());
			$stmt->bindParam(":uid", $uid);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				$result = $stmt->fetch(PDO::FETCH_OBJ);
				if(date('d',time()) == date('d',strtotime($result->timestamp))){
					return false;
				}else{
					return true;
				}
			}else{
				return true;
			}
		} catch(PDOException $e) {
			throw new Exception('Question: DB: checkReattempt: '.$e->getMessage());
		}
		return null;
	}

	public function addImage($type, $ext) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO image(type, relation, relation_id, ext) VALUES (?,?,?,?)";
			$stmt = $dbo->prepare($sql);
			$stmt->execute(array($type, 'Question', $this->get_id(), $ext));
			$imageid = $dbo->lastInsertId('id');
			$dbo->commit();
			return $imageid;
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: DB: AddImage: '.$e->getMessage());
		}
		return null;
	}

	public function getImage() {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM image WHERE relation = 'Question' AND relation_id = ?";
			$stmt = $dbo->prepare($sql);
			$stmt->execute(array($this->get_id()));
			return $stmt->fetchAll();
		} catch(PDOException $e) {
			throw new Exception('Question: DB: GetImage: '.$e->getMessage());
		}
		return null;
	}

	public static function getType($id){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT type FROM question q WHERE qid = :qid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $id);
			$stmt->execute();
			if($stmt->rowCount() > 0)
				return $stmt->fetch(PDO::FETCH_OBJ)->type;
			else
				return null;
		} catch(PDOException $e) {
			throw new Exception('Question: GetType: '.$e->getMessage());
		}
		return null;
	}

	public static function getAttemptHistory($uid, $qid){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM question q LEFT JOIN attempt_history h ON h.qid=q.qid WHERE h.uid = :uid AND h.qid = :qid ORDER BY timestamp DESC";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $uid);
			$stmt->bindParam(':qid', $qid);
			$stmt->execute();
			if($stmt->rowCount() > 0)
				return $stmt->fetch(PDO::FETCH_OBJ);
			else
				return null;
		} catch(PDOException $e) {
			throw new Exception('Question: GetAttempt: '.$e->getMessage());
		}
		return null;
	}

	public static function get_types($type = null) {
		$all_type = array(
			'MultipleChoice' => "多項選擇題", 
			'QuestionAnswer' => "問答題", 
			'TrueOrFalse' => "是非題",
			'Matching' => "配對題",
			'Rephrasing' => "重組句子",
			//'OpenQuestion' => "開放式問題",
			'Punctuation' => "標點符號");
		if(!$type)
			return $all_type;
		else
			return $all_type[$type];
	}

	abstract public function getName();
	abstract public function getQuestion();
	abstract public function getModelAnswer();
	abstract public function getAnswers();

}

class MultipleChoice extends Question
{
	public function __construct($qid){
		error_log("Question Instance: MC, ID: ".$qid);
		parent::__construct($qid);
	}

	public function getName(){
		return $this->object->name;
	}

	public function getQuestion(){
		return $this->object->description;
	}

	public function getModelAnswer(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: MC GetModelAnswer: '.$e->getMessage());
		}
		return null;
	}

	public function getAnswers(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 0";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			return $stmt->fetchAll();
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: MC GetAnswer: '.$e->getMessage());
		}
		return null;
	}

	public function checkAnswer($answer){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			$model_answer = $stmt->fetchAll();
			$valid = array();
			foreach ($model_answer as $a) {
				array_push($valid, $a['answer']);
			}
			return (count(array_diff($answer, $valid)) == 0);
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: MC GetAnswer: '.$e->getMessage());
		}
	}

	public function attempt($uid, $answer){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$result = $this->checkAnswer($answer);
			if(!$this->checkReattempt($uid)) return $result;
			$sql = "INSERT INTO attempt_history(uid, qid, answer, correct) VALUES (:uid,:qid,:answer,:correct) ON DUPLICATE KEY UPDATE answer = :new_answer, timestamp = CURRENT_TIMESTAMP, correct = :new_correct, attempt = attempt + 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $uid);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->bindParam(':answer', implode('|::|', $answer));
			$stmt->bindParam(':new_answer', implode('|::|', $answer));
			$result = ($result?1:0);
			$stmt->bindParam(':correct', $result);
			$stmt->bindParam(':new_correct', $result);
			$stmt->execute();
			$dbo->commit();
			return $result;
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: MC: Attempt: '.$e->getMessage());
		}
		return null;
	}

	public static function newquestion($name, $description, $modelAnswer, $answer, $qsid){
		// var_dump($answer);
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO question(type, name, description) VALUES ('MultipleChoice', :name, :description)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':description', $description);
			$stmt->execute();
			$qid = $dbo->lastInsertId('qid');
			// var_dump($qid, $name, $description);
			$sqlInsertAnswer = 'INSERT INTO answer(qid, answer, isModelAnswer) VALUES (?,?,?)';
			$stmt = $dbo->prepare($sqlInsertAnswer);
			foreach ($answer as $a) {
				$stmt->execute(array($qid, $a, 0));
			}
			foreach ($modelAnswer as $a) {
				$stmt->execute(array($qid, $a, 1));
			}
			$sql = "INSERT INTO question_set_relation(qid, qsid) VALUES (:qid, :qsid)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $qid);
			$stmt->bindParam(':qsid', $qsid);
			$stmt->execute();
			$dbo->commit();
			return new self($qid);
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Add MC: '.$e->getMessage());
		}
		return null;
	}
}

class QuestionAnswer extends Question
{
	public function __construct($qid){
		error_log("Question Instance: QA, ID: ".$qid);
		parent::__construct($qid);
	}

	public function getName(){
		return $this->object->name;
	}

	public function getQuestion(){
		return $this->object->description;
	}

	public function getModelAnswer(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			return $stmt->fetchAll();
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: MC GetModelAnswer: '.$e->getMessage());
		}
		return null;
	}

	public function getAnswers(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 0";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			return $stmt->fetchAll();
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: MC GetAnswer: '.$e->getMessage());
		}
		return null;
	}

	public function checkAnswer($answer){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			$model_answer = $stmt->fetchAll();
			foreach ($model_answer as $a) {
				if($a['answer'] == $answer) return true;
			}
			return false;
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: QA GetAnswer: '.$e->getMessage());
		}
	}

	public function attempt($uid, $answer){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$result = $this->checkAnswer($answer);
			if(!$this->checkReattempt($uid)) return $result;
			$sql = "INSERT INTO attempt_history(uid, qid, answer, correct) VALUES (:uid,:qid,:answer,:correct) ON DUPLICATE KEY UPDATE answer = :new_answer, timestamp = CURRENT_TIMESTAMP, correct = :new_correct, attempt = attempt + 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $uid);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->bindParam(':answer', $answer);
			$stmt->bindParam(':new_answer', $answer);
			$result = ($result?1:0);
			$stmt->bindParam(':correct', $result);
			$stmt->bindParam(':new_correct', $result);
			$stmt->execute();
			$dbo->commit();
			return $result;
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: QA: Attempt: '.$e->getMessage());
		}
		return null;
	}

	public static function newquestion($name, $description, $modelAnswer, $answer, $qsid){
		// var_dump($answer);
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO question(type, name, description) VALUES ('QuestionAnswer', :name, :description)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':description', $description);
			$stmt->execute();
			$qid = $dbo->lastInsertId('qid');
			// var_dump($qid, $name, $description);
			$sqlInsertAnswer = 'INSERT INTO answer(qid, answer, isModelAnswer) VALUES (?,?,?)';
			$stmt = $dbo->prepare($sqlInsertAnswer);
			foreach ($answer as $a) {
				$stmt->execute(array($qid, $a, 0));
			}
			foreach ($modelAnswer as $a) {
				$stmt->execute(array($qid, $a, 1));
			}
			$sql = "INSERT INTO question_set_relation(qid, qsid) VALUES (:qid, :qsid)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $qid);
			$stmt->bindParam(':qsid', $qsid);
			$stmt->execute();
			$dbo->commit();
			return new self($qid);
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Add QA: '.$e->getMessage());
		}
		return null;
	}
}

class TrueOrFalse extends Question
{
	public function __construct($qid){
		error_log("Question Instance: TF, ID: ".$qid);
		parent::__construct($qid);
	}

	public function getName(){
		return $this->object->name;
	}

	public function getQuestion(){
		return $this->object->description;
	}

	public function getModelAnswer(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			return $stmt->fetchAll();
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: MC GetModelAnswer: '.$e->getMessage());
		}
		return null;
	}

	public function getAnswers(){
		return $this->getModelAnswer();
	}

	public function checkAnswer($answer){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			$model_answer = $stmt->fetchAll();
			foreach ($model_answer as $a) {
				if($a['answer'] == $answer) return true;
			}
			return false;
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: TF GetAnswer: '.$e->getMessage());
		}
	}

	public function attempt($uid, $answer){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$result = $this->checkAnswer($answer);
			if(!$this->checkReattempt($uid)) return $result;
			$sql = "INSERT INTO attempt_history(uid, qid, answer, correct) VALUES (:uid,:qid,:answer,:correct) ON DUPLICATE KEY UPDATE answer = :new_answer, timestamp = CURRENT_TIMESTAMP, correct = :new_correct, attempt = attempt + 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $uid);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->bindParam(':answer', $answer);
			$stmt->bindParam(':new_answer', $answer);
			$result = ($result?1:0);
			$stmt->bindParam(':correct', $result);
			$stmt->bindParam(':new_correct', $result);
			$stmt->execute();
			$dbo->commit();
			return $result;
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: TF: Attempt: '.$e->getMessage());
		}
		return null;
	}

	public static function newquestion($name, $description, $modelAnswer, $answer, $qsid){
		// var_dump($answer);
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO question(type, name, description) VALUES ('TrueOrFalse', :name, :description)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':description', $description);
			$stmt->execute();
			$qid = $dbo->lastInsertId('qid');
			// var_dump($qid, $name, $description);
			$sqlInsertAnswer = 'INSERT INTO answer(qid, answer, isModelAnswer) VALUES (?,?,?)';
			$stmt = $dbo->prepare($sqlInsertAnswer);
			foreach ($modelAnswer as $a) {
				$stmt->execute(array($qid, $a, 1));
			}
			$sql = "INSERT INTO question_set_relation(qid, qsid) VALUES (:qid, :qsid)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $qid);
			$stmt->bindParam(':qsid', $qsid);
			$stmt->execute();
			$dbo->commit();
			return new self($qid);
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Add QA: '.$e->getMessage());
		}
		return null;
	}
}

class Matching extends Question
{
	public function __construct($qid){
		error_log("Question Instance: Matching, ID: ".$qid);
		parent::__construct($qid);
	}

	public function getName(){
		return $this->object->name;
	}

	public function getQuestion(){
		return $this->object->description;
	}

	public function getModelAnswer(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			$pairs = array();
			while($pair = $stmt->fetch(PDO::FETCH_OBJ)){
				array_push($pairs, explode('|:|', $pair->answer));
			}
			return $pairs;
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Matching GetModelAnswer: '.$e->getMessage());
		}
		return null;
	}

	public function getAnswers(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 0";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			return $stmt->fetchAll();
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Matching GetAnswer: '.$e->getMessage());
		}
		return null;
	}

	public function getShuffledAnswers(){
		$left = array();
		$right = array();
		foreach($this->getModelAnswer() as $a){
			array_push($left, $a[0]);
			array_push($right, $a[1]);
		}
		foreach ($this->getAnswers() as $a) {
			array_push($left, $a['answer']);
			array_push($right, $a['answer']);
		}
		shuffle($left);
		shuffle($right);
		return array($left, $right);
	}

	public function checkAnswer($answer){
		$modelAnswer = $this->getModelAnswer();
		$ma_clear = array();
		$a_clear = array();
		foreach ($answer as $i => $a) {
			$t1 = array_search($a, $modelAnswer);
			if($t1 !== false){
				array_push($ma_clear, $t1);
				array_push($a_clear, $i);
				continue;
			}
			$t1 = array_search(array_reverse($a), $modelAnswer);
			if($t1 !== false){
				array_push($ma_clear, $t1);
				array_push($a_clear, $i);
				continue;
			}
		}
		foreach($ma_clear as $c) unset($modelAnswer[$c]);
		foreach($a_clear as $c) unset($answer[$c]);
		return !(count($modelAnswer) + count($answer) > 0);
	}

	public function attempt($uid, $answer){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$result = $this->checkAnswer($answer);
			if(!$this->checkReattempt($uid)) return $result;
			$ans = array();
			foreach($answer as $a){
				array_push($ans, $a[0].'|:|'.$a[1]);
			}
			$sql = "INSERT INTO attempt_history(uid, qid, answer, correct) VALUES (:uid,:qid,:answer,:correct) ON DUPLICATE KEY UPDATE answer = :new_answer, timestamp = CURRENT_TIMESTAMP, correct = :new_correct, attempt = attempt + 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $uid);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->bindParam(':answer', implode('|::|', $ans));
			$stmt->bindParam(':new_answer', implode('|::|', $ans));
			$result = ($result?1:0);
			$stmt->bindParam(':correct', $result);
			$stmt->bindParam(':new_correct', $result);
			$stmt->execute();
			$dbo->commit();
			return $result;
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Matching: Attempt: '.$e->getMessage());
		}
		return null;
	}

	public static function newquestion($name, $description, $modelAnswer, $answer, $qsid){
		// var_dump($answer);
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO question(type, name, description) VALUES ('Matching', :name, :description)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':description', $description);
			$stmt->execute();
			$qid = $dbo->lastInsertId('qid');
			// var_dump($qid, $name, $description);
			$sqlInsertAnswer = 'INSERT INTO answer(qid, answer, isModelAnswer) VALUES (?,?,?)';
			$stmt = $dbo->prepare($sqlInsertAnswer);
			foreach ($answer as $a) {
				if($a == '') continue;
				$stmt->execute(array($qid, $a, 0));
			}
			foreach ($modelAnswer as $a) {
				$stmt->execute(array($qid, $a, 1));
			}
			$sql = "INSERT INTO question_set_relation(qid, qsid) VALUES (:qid, :qsid)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $qid);
			$stmt->bindParam(':qsid', $qsid);
			$stmt->execute();
			$dbo->commit();
			return new self($qid);
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Add Matching: '.$e->getMessage());
		}
		return null;
	}
}

class Rephrasing extends Question
{
	public function __construct($qid){
		error_log("Question Instance: Rephrasing, ID: ".$qid);
		parent::__construct($qid);
	}

	public function getName(){
		return $this->object->name;
	}

	public function getQuestion(){
		return $this->object->description;
	}

	public function getModelAnswer(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			$pairs = array();
			while($pair = $stmt->fetch(PDO::FETCH_OBJ)){
				array_push($pairs, explode('|:|', $pair->answer));
			}
			return $pairs;
		} catch(PDOException $e) {
			throw new Exception('Question: Rephrasing GetModelAnswer: '.$e->getMessage());
		}
		return null;
	}

	public function getAnswers(){
		$output = array();
		foreach($this->getModelAnswer() as $answer) {
			array_push($output, $answer[1]);
		}
		shuffle($output);
		return $output;
	}

	public function checkAnswer($answer){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT answer FROM answer WHERE qid = :qid AND isModelAnswer = 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			$pairs = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
			return (count(array_diff($pairs, $answer)) == 0);
		} catch(PDOException $e) {
			throw new Exception('Question: Rephrasing CheckAnswer: '.$e->getMessage());
		}
		return false;
	}

	public function attempt($uid, $answer){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$result = $this->checkAnswer($answer);
			if(!$this->checkReattempt($uid)) return $result;
			$sql = "INSERT INTO attempt_history(uid, qid, answer, correct) VALUES (:uid,:qid,:answer,:correct) ON DUPLICATE KEY UPDATE answer = :new_answer, timestamp = CURRENT_TIMESTAMP, correct = :new_correct, attempt = attempt + 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $uid);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->bindParam(':answer', implode('|::|', $answer));
			$stmt->bindParam(':new_answer', implode('|::|', $answer));
			$result = ($result?1:0);
			$stmt->bindParam(':correct', $result);
			$stmt->bindParam(':new_correct', $result);
			$stmt->execute();
			$dbo->commit();
			return $result;
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Rephrasing: Attempt: '.$e->getMessage());
		}
		return null;
	}

	public static function newquestion($name, $description, $modelAnswer, $answer, $qsid){
		// var_dump($answer);
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO question(type, name, description) VALUES ('Rephrasing', :name, :description)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':description', $description);
			$stmt->execute();
			$qid = $dbo->lastInsertId('qid');
			// var_dump($qid, $name, $description);
			$sqlInsertAnswer = 'INSERT INTO answer(qid, answer, isModelAnswer) VALUES (?,?,?)';
			$stmt = $dbo->prepare($sqlInsertAnswer);
			foreach ($answer as $a) {
				$stmt->execute(array($qid, $a, 0));
			}
			foreach ($modelAnswer as $a) {
				$stmt->execute(array($qid, $a, 1));
			}
			$sql = "INSERT INTO question_set_relation(qid, qsid) VALUES (:qid, :qsid)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $qid);
			$stmt->bindParam(':qsid', $qsid);
			$stmt->execute();
			$dbo->commit();
			return new self($qid);
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Add Rephrasing: '.$e->getMessage());
		}
		return null;
	}
}

class OpenQuestion extends Question
{
	public function __construct($qid){
		error_log("Question Instance: OpenQuestion, ID: ".$qid);
		parent::__construct($qid);
	}

	public function getName(){
		return $this->object->name;
	}

	public function getQuestion(){
		return $this->object->description;
	}

	public function getModelAnswer(){
		return null;
	}

	public function getAnswers(){
		return null;
	}

	public function checkAnswer($answer){
		return true;
	}

	public function attempt($uid, $answer){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$result = $this->checkAnswer($answer);
			$sql = "INSERT INTO attempt_history(uid, qid, answer, correct) VALUES (:uid,:qid,:answer,:correct) ON DUPLICATE KEY UPDATE answer = :new_answer, timestamp = CURRENT_TIMESTAMP, correct = :new_correct";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $uid);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->bindParam(':answer', $answer);
			$stmt->bindParam(':new_answer', $answer);
			$result = ($result?1:0);
			$stmt->bindParam(':correct', $result);
			$stmt->bindParam(':new_correct', $result);
			$stmt->execute();
			$dbo->commit();
			return $result;
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Attempt: '.$e->getMessage());
		}
		return null;
	}

	public static function newquestion($name, $description, $modelAnswer, $answer, $qsid){
		// var_dump($answer);
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO question(type, name, description) VALUES ('OpenQuestion', :name, :description)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':description', $description);
			$stmt->execute();
			$qid = $dbo->lastInsertId('qid');
			$sql = "INSERT INTO question_set_relation(qid, qsid) VALUES (:qid, :qsid)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $qid);
			$stmt->bindParam(':qsid', $qsid);
			$stmt->execute();
			$dbo->commit();
			return new self($qid);
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Add OpenQuestion: '.$e->getMessage());
		}
		return null;
	}
}

class Punctuation extends Question
{
	public function __construct($qid){
		error_log("Question Instance: Punctuation, ID: ".$qid);
		parent::__construct($qid);
	}

	public function getName(){
		return $this->object->name;
	}

	public function getQuestion(){
		return $this->object->description;
	}

	public function getModelAnswer(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_OBJ);
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Punctuation GetModelAnswer: '.$e->getMessage());
		}
		return null;
	}

	public function getAnswers(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 0";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_OBJ);
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Punctuation GetModelAnswer: '.$e->getMessage());
		}
		return null;
	}

	public function checkAnswer($answer){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM answer WHERE qid = :qid AND isModelAnswer = 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->execute();
			$model_answer = $stmt->fetch(PDO::FETCH_OBJ);
			if($model_answer->answer == $answer) return true;
			return false;
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Punctuation GetAnswer: '.$e->getMessage());
		}
	}

	public function attempt($uid, $answer){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$result = $this->checkAnswer($answer);
			if(!$this->checkReattempt($uid)) return $result;
			$sql = "INSERT INTO attempt_history(uid, qid, answer, correct) VALUES (:uid,:qid,:answer,:correct) ON DUPLICATE KEY UPDATE answer = :new_answer, timestamp = CURRENT_TIMESTAMP, correct = :new_correct, attempt = attempt + 1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $uid);
			$stmt->bindParam(':qid', $this->get_id());
			$stmt->bindParam(':answer', $answer);
			$stmt->bindParam(':new_answer', $answer);
			$result = ($result?1:0);
			$stmt->bindParam(':correct', $result);
			$stmt->bindParam(':new_correct', $result);
			$stmt->execute();
			$dbo->commit();
			return $result;
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Punctuation: Attempt: '.$e->getMessage());
		}
		return null;
	}

	public static function newquestion($name, $description, $modelAnswer, $answer, $qsid){
		// var_dump($answer);
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO question(type, name, description) VALUES ('Punctuation', :name, :description)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':description', $description);
			$stmt->execute();
			$qid = $dbo->lastInsertId('qid');
			// var_dump($qid, $name, $description);
			$sqlInsertAnswer = 'INSERT INTO answer(qid, answer, isModelAnswer) VALUES (?,?,1)';
			$stmt = $dbo->prepare($sqlInsertAnswer);
			$stmt->execute(array($qid, $modelAnswer));
			$sqlInsertAnswer = 'INSERT INTO answer(qid, answer, isModelAnswer) VALUES (?,?,0)';
			$stmt = $dbo->prepare($sqlInsertAnswer);
			$stmt->execute(array($qid, $answer));
			$sql = "INSERT INTO question_set_relation(qid, qsid) VALUES (:qid, :qsid)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qid', $qid);
			$stmt->bindParam(':qsid', $qsid);
			$stmt->execute();
			$dbo->commit();
			return new self($qid);
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('Question: Add Punctuation: '.$e->getMessage());
		}
		return null;
	}
}