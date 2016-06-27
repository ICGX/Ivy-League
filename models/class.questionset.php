<?php

require_once 'class.question.php';

class QuestionSet
{
	protected $question_set_id;
	protected $question_set_name;
	protected $question_set_description;
	protected $question_set_level_read;
	protected $question_set_level_write;
	protected $teacher;

	function __construct($id = null, $name = null, $levelRead = null, $levelWrite = null, $added_by = null, $description = null, $subject = null, $grade = null){
		if(isset($name) && isset($levelRead) && isset($levelWrite) && isset($added_by)&& isset($description)&& isset($subject)&& isset($grade)){
			// New ID
			$this->new_question_set($name, $levelRead, $levelWrite, $added_by, $description, $subject, $grade);
		}elseif(isset($id)){
			// Get QS from DB
			$this->question_set_id = $id;
			$this->refresh();
		}else{
			throw new Exception("QuestionSet: missing argument");
		}
	}

	private function refresh(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$sql = "SELECT * FROM question_set WHERE qsid = :qsid";
		$stmt = $dbo->prepare($sql);
		$stmt->bindParam(':qsid', $this->question_set_id);
		$stmt->execute();
		$affected = $stmt->rowCount();
		if($affected < 1) throw new Exception('QuestionSet: Question set not found');
		if($affected > 1) throw new Exception('QuestionSet: Multiple question set returned, please check the primary key');
		$row = $stmt->fetch(PDO::FETCH_OBJ);
		$this->question_set_name = $row->name;
		$this->question_set_description = $row->description;
		$this->question_set_level_read = $row->levelRead;
		$this->question_set_level_write = $row->levelWrite;
		$this->teacher = $row->added_by;
	}

	private function new_question_set($name, $levelRead, $levelWrite, $added_by, $description, $subject, $grade){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO question_set(name, levelRead, levelWrite, added_by, description, subject, grade) VALUES (:name, :levelRead, :levelWrite, :added_by, :description, :subject, :grade)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':levelRead', $levelRead);
			$stmt->bindParam(':levelWrite', $levelWrite);
			$stmt->bindParam(':added_by', $added_by);
			$stmt->bindParam(':description', $description);
			$stmt->bindParam(':subject', $subject);
			$stmt->bindParam(':grade', $grade);
			$stmt->execute();
			$this->question_set_id = $dbo->lastInsertId('qsid');
			$dbo->commit();
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('QuestionSet: NewSet: '.$e->getMessage());
		}
	}

	public function get_teacher(){
		return $this->teacher;
	}

	public function remove_question_set(){
		foreach($this->get_questions() as $q){
			$q->remove($this->get_id());
		}
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "DELETE FROM lecture_revision WHERE relation_id = :qsid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qsid', $this->get_id());
			$stmt->execute();
			$sql = "DELETE FROM image WHERE relation = 'QuestionSet' AND type = 'thumbnail_image' AND relation_id = :qsid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qsid', $this->get_id());
			$stmt->execute();
			$sql = "DELETE FROM question_set WHERE qsid = :qsid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qsid', $this->get_id());
			$stmt->execute();
			$dbo->commit();
		}catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('QuestionSet: RemoveSet: '.$e->getMessage());
		}
	}

	public function get_question($seq){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$sql = "SELECT qsr.*, q.type FROM question_set_relation qsr LEFT JOIN question q ON qsr.qid = q.qid WHERE qsid = :qsid ORDER BY q.qid ASC";
		$stmt = $dbo->prepare($sql);
		$stmt->bindParam(':qsid', $this->question_set_id);
		$stmt->execute();
		$affected = $stmt->rowCount();
		if($seq >= $affected) return null;
		$i = 0;
		while($row = $stmt->fetch(PDO::FETCH_OBJ)){
			if($seq == $i) return $row;
			$i++;
		}
	}

	public function get_questions_count(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$sql = "SELECT qsr.*, q.type FROM question_set_relation qsr LEFT JOIN question q ON qsr.qid = q.qid WHERE qsid = :qsid ORDER BY q.qid ASC";
		$stmt = $dbo->prepare($sql);
		$stmt->bindParam(':qsid', $this->question_set_id);
		$stmt->execute();
		return $stmt->rowCount();
	}

	public function get_questions(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$sql = "SELECT qsr.*, q.type FROM question_set_relation qsr LEFT JOIN question q ON qsr.qid = q.qid WHERE qsid = :qsid ORDER BY q.qid ASC";
		$stmt = $dbo->prepare($sql);
		$stmt->bindParam(':qsid', $this->question_set_id);
		$stmt->execute();
		$affected = $stmt->rowCount();
		$result = array();
		while ($q = $stmt->fetch(PDO::FETCH_OBJ)) {
			array_push($result, new $q->type($q->qid));
		}
		return $result;
	}

	public function get_subject(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$sql = "SELECT qs.subject, s.name FROM question_set qs LEFT JOIN subject s ON qs.subject = s.id WHERE qs.qsid = :qsid";
		$stmt = $dbo->prepare($sql);
		$stmt->bindParam(':qsid', $this->question_set_id);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function get_grade(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$sql = "SELECT grade FROM question_set WHERE qsid = :qsid";
		$stmt = $dbo->prepare($sql);
		$stmt->bindParam(':qsid', $this->question_set_id);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function get_questions_attempts(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM question_set_relation qsr LEFT JOIN attempt_history ah WHERE qsr.qsid = :qsid AND ah.uid = :uid ORDER BY ah.timestamp DESC";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qsid', $this->get_id());
			$stmt->bindParam(':uid', $uid);
			$stmt->execute();
			if($stmt->rowCount() > 0)
				return $stmt->fetchALL(PDO::FETCH_ASSOC);
			else
				return null;
		}catch(PDOException $e) {
			throw new Exception('QuestionSet: GetAttempt: '.$e->getMessage());
		}
	}

	// public function attempt($uid){
	// 	$db = new DATABASE();
	// 	$dbo = $db->getDBO();
	// 	$dbo->beginTransaction();
	// 	try{
	// 		$sql = "INSERT INTO logining.attempt(uid, qsid, attempt_count) VALUES(:uid, :qsid, 0) ON DUPLICATE KEY UPDATE attempt_count = attempt_count+1 , fail_count = fail_count+1";
	// 		$stmt = $dbo->prepare($sql);
	// 		$stmt->bindParam(':qsid', $this->get_id());
	// 		$stmt->bindParam(':uid', $uid);
	// 		$stmt->execute();
	// 		$dbo->commit();
	// 	}catch(PDOException $e) {
	// 		$dbo->rollback();
	// 		throw new Exception('QuestionSet: Attempt: '.$e->getMessage());
	// 	}
	// }

	public function notifyParent($uid){
		require_once 'class.user.php';
		$types = Question::get_types();
		$user = new User($uid);
		$profile = $user->get_parent();
		$parent_email = $profile->email;
		$headers  = "MIME-Version: 1.0\r\n";
    	$headers .= "Content-type: text/html; charset=UTF-8\r\n";
		$headers .= "From:info@ivyleague.com";
		$subject = "=?UTF-8?B?".base64_encode("Ivy League 家長提示")."?=";
		$content = "<p>親愛的 " . $profile->name . "家長：</p>";
		$content .= "<p>根據系統記錄， 貴子弟使用本系統練習題目，答題錯誤超過三次。題目如下：</p><br>";
		$questions = $this->get_questions();
		foreach($questions as $question){
			$attempt = Question::getAttemptHistory($uid, $question->get_id());
			// var_dump($attempt);
			if($attempt){
				$content .= '<p>'.$types[$attempt->type]." - ".$attempt->name."</p>";
			}else{
				$content .= '<p>'.$types[$question->get_type()]." - ".$question->get_name()." (未完成)</p>";
			}
		}
		$content .= "<br>";
		$content .= "<p>請督促 貴子弟勤加努力，並有效運用IvyLeague..... 如需協助，請聯絡......</p>";
		$content .= "<p>Ivy League</p>";
		mail($parent_email,$subject,$content,$headers);
	}

	public function notifyUser($uid){
		require_once 'class.user.php';
		$types = Question::get_types();
		$user = new User($uid);
		$profile = $user->get_profile();
		$email = $profile->email;
		$headers  = "MIME-Version: 1.0\r\n";
    	$headers .= "Content-type: text/html; charset=UTF-8\r\n";
		$headers .= "From:info@ivyleague.com";
		$subject = "=?UTF-8?B?".base64_encode("Ivy League 練習提示")."?=";
		$content = "<p>親愛的 " . $profile->name . "：</p>";
		$content .= "<p>根據系統記錄， 你使用本系統練習題目，答題錯誤超過三次。題目如下：</p><br>";
		$questions = $this->get_questions();
		foreach($questions as $question){
			$attempt = Question::getAttemptHistory($uid, $question->get_id());
			// var_dump($attempt);
			if($attempt){
				$content .= '<p>'.$types[$attempt->type]." - ".$attempt->name."</p>";
			}else{
				$content .= '<p>'.$types[$question->get_type()]." - ".$question->get_name()." (未完成)</p>";
			}
		}
		$content .= "<br>";
		$content .= "<p>請勤加努力，並有效運用IvyLeague..... 如需協助，請聯絡......</p>";
		$content .= "<p>Ivy League</p>";
		mail($email,$subject,$content,$headers);
	}

	public function notifyTeacher($uid){
		require_once 'class.user.php';
		$student = new User($uid);
		$student_profile = $student->get_profile();

		$types = Question::get_types();
		$user = new User($this->teacher);
		$profile = $user->get_profile();
		$email = $profile->email;
		$headers  = "MIME-Version: 1.0\r\n";
    	$headers .= "Content-type: text/html; charset=UTF-8\r\n";
		$headers .= "From:info@ivyleague.com";
		$subject = "=?UTF-8?B?".base64_encode("Ivy League 導師提示")."?=";
		$content = "<p>親愛的 " . $profile->name . "：</p>";
		$content .= "<p>根據系統記錄， 學生 ".$student_profile->name." 使用本系統練習題目，答題錯誤超過三次。題目如下：</p><br>";
		$questions = $this->get_questions();
		foreach($questions as $question){
			$attempt = Question::getAttemptHistory($uid, $question->get_id());
			if($attempt){
				$content .= '<p>'.$types[$attempt->type]." - ".$attempt->name."</p>";
			}else{
				$content .= '<p>'.$types[$question->get_type()]." - ".$question->get_name()." (未完成)</p>";
			}
		}
		$content .= "<br>";
		$content .= "<p>如需協助，請聯絡......</p>";
		$content .= "<p>Ivy League</p>";
		mail($email,$subject,$content,$headers);
	}

	public function failed($uid) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO logining.attempt(uid, qsid, attempt_count) VALUES(:uid, :qsid, 0) ON DUPLICATE KEY UPDATE fail_count = fail_count+1";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qsid', $this->get_id());
			$stmt->bindParam(':uid', $uid);
			$stmt->execute();
			$sql = "SELECT * FROM logining.attempt WHERE uid = :uid AND qsid = :qsid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qsid', $this->get_id());
			$stmt->bindParam(':uid', $uid);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				$attempt = $stmt->fetch(PDO::FETCH_OBJ);
				// error_log('ATTEMPT: '.$attempt->fail_count);
				if($attempt->fail_count == 3){ 
					$this->notifyParent($uid);
					$this->notifyUser($uid);
					$this->notifyTeacher($uid);
				}
			}
			$dbo->commit();
		}catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('QuestionSet: FailAttempt: '.$e->getMessage());
		}
	}

	public function finished($uid) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "UPDATE attempt SET finish = 1 WHERE uid = :uid AND qsid = :qsid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qsid', $this->get_id());
			$stmt->bindParam(':uid', $uid);
			$stmt->execute();
			$dbo->commit();
		}catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('QuestionSet: FinishQS: '.$e->getMessage());
		}
	}

	public function started($uid){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO attempt(uid, qsid, attempt_count, fail_count, finish) VALUES (:uid, :qsid, 1, 0, 0)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':uid', $uid);
			$stmt->bindParam(':qsid', $this->get_id());
			$stmt->execute();
			$dbo->commit();
		}catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('QuestionSet: AttemptQS: '.$e->getMessage());
		}
	}

	public function attempt($uid, $failed) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$today = strtotime(date('Y-m-d', time()));
			$today1 = $today+86400;
			$sql = "SELECT * FROM attempt WHERE uid = :uid AND qsid = :qsid AND lastsubmit >= FROM_UNIXTIME(:ts) AND lastsubmit < FROM_UNIXTIME(:ts1)";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qsid', $this->get_id());
			$stmt->bindParam(':uid', $uid);
			$stmt->bindParam(':ts', $today);
			$stmt->bindParam(':ts1', $today1);
			$stmt->execute();
			$attempted = false;
			if($stmt->rowCount() > 0){
				$attempted = true;
				$attempt = $stmt->fetch(PDO::FETCH_OBJ);
				$sql = "UPDATE attempt SET attempt_count = attempt_count + 1, fail_count = fail_count + :fc, finish = ".($failed?"0":"1")." WHERE uid = :uid AND qsid = :qsid";
				$stmt = $dbo->prepare($sql);
				$stmt->bindParam(':fc', $failed);
				$stmt->bindParam(':uid', $uid);
				$stmt->bindParam(':qsid', $this->get_id());
				$stmt->execute();
				if($attempt->fail_count + $failed == 3){
					$this->notifyParent($uid);
					$this->notifyUser($uid);
					$this->notifyTeacher($uid);
				} 
			}else{
				$attempt = $stmt->fetch(PDO::FETCH_OBJ);
				$sql = "INSERT INTO attempt(uid, qsid, attempt_count, fail_count, finish) VALUES (:uid, :qsid, 1, ".($failed?"1":"0").", ".($failed?"0":"1").")";
				$stmt = $dbo->prepare($sql);
				$stmt->bindParam(':uid', $uid);
				$stmt->bindParam(':qsid', $this->get_id());
				$stmt->execute();
			}
			$dbo->commit();
			return $attempted;
		}catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('QuestionSet: AttemptQS: '.$e->getMessage());
		}
	}

	public function score($uid, $percentage) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM award WHERE uid = :uid AND qsid = :qsid AND `timestamp` <= CURRENT_TIMESTAMP AND `timestamp` + 86400 > CURRENT_TIMESTAMP";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qsid', $this->get_id());
			$stmt->bindParam(':uid', $uid);
			$stmt->execute();
			if($stmt->rowCount() == 0){
				echo "NOT EXIST!!!";
				echo $percentage;
				$score = '0';
				if($percentage >= 90)
					$score = '5';
				elseif($percentage >= 80)
					$score = '4';
				elseif($percentage >= 70)
					$score = '3';
				elseif($percentage >= 60)
					$score = '2';
				elseif($percentage >= 50)
					$score = '1';
				$sql = "INSERT INTO award(uid, qsid, score, timestamp) VALUES (:uid, :qsid, :score, CURRENT_TIMESTAMP)";
				$stmt1 = $dbo->prepare($sql);
				$stmt1->bindParam(':uid', $uid);
				$stmt1->bindParam(':qsid', $this->get_id());
				$stmt1->bindParam(':score', $score);
				$stmt1->execute();

			}else{
				$result = $stmt->fetch(PDO::FETCH_OBJ);
				return array(false, $result->score);
			}
			$dbo->commit();
			return array(true, $score);
		}catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('QuestionSet: Score: '.$e->getMessage());
		}
	}

	public function get_attempt($uid){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			// var_dump($uid);
			// var_dump($this->get_id());
			$sql = "SELECT * FROM attempt WHERE qsid = :qsid AND uid = :uid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qsid', $this->get_id());
			$stmt->bindParam(':uid', $uid);
			$stmt->execute();
			if($stmt->rowCount() > 0)
				return $stmt->fetch(PDO::FETCH_OBJ);
			else
				return null;
		}catch(PDOException $e) {
			throw new Exception('QuestionSet: GetAttempt: '.$e->getMessage());
		}
	}

	public function get_attempts_detail($uid){

		$qs_attempt = $this->get_attempt($uid);
		$questions = $this->get_questions();
		$result = array('qs_attempt'=>$qs_attempt, 'q_attempt'=>array());
		// var_dump($qs_attempt);
		// var_dump($questions);
		foreach($questions as $question){
			// echo '<pre>';
			$question_attempt = Question::getAttemptHistory($uid, $question->get_id());
			if(!$question_attempt) $question_attempt == $question;
			// echo $question->get_id() . "\n";
			// var_dump($question_attempt);
			// var_dump($question);
			// echo '</pre>';
			$result['q_attempt'][$question->get_id()] = $question_attempt;
		}
		return $result;
	}

	public function addImage($type, $ext) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "INSERT INTO image(type, relation, relation_id, ext) VALUES (?,?,?,?)";
			$stmt = $dbo->prepare($sql);
			$stmt->execute(array($type, 'QuestionSet', $this->get_id(), $ext));
			$imageid = $dbo->lastInsertId('id');
			$dbo->commit();
			return $imageid;
		} catch(PDOException $e) {
			$dbo->rollback();
			throw new Exception('QuestionSet: DB: AddImage: '.$e->getMessage());
		}
		return null;
	}

	public function getThumbnail() {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = 'SELECT * FROM image WHERE relation = "QuestionSet" AND type = "thumbnail_image" AND relation_id = :id';
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':id', $this->get_id());
			$stmt->execute();
			if($stmt->rowCount() > 0){
				return $stmt->fetch(PDO::FETCH_OBJ);
			}else{
				return null;
			}
		}catch(PDOException $e) {
			throw new Exception('QuestionSet: GetThumbnail: '.$e->getMessage());
		}
	}

	public function getLevelWrite(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = 'SELECT levelWrite FROM question_set WHERE qsid = :qsid';
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qsid', $this->get_id());
			$stmt->execute();
			if($stmt->rowCount() > 0){
				return $stmt->fetch(PDO::FETCH_ASSOC)[0];
			}else{
				return null;
			}
		}catch(PDOException $e) {
			throw new Exception('QuestionSet: getLevelWrite: '.$e->getMessage());
		}
	}

	public function getLevelRead(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = 'SELECT levelRead FROM question_set WHERE qsid = :qsid';
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':qsid', $this->get_id());
			$stmt->execute();
			if($stmt->rowCount() > 0){
				return $stmt->fetch(PDO::FETCH_ASSOC)[0];
			}else{
				return null;
			}
		}catch(PDOException $e) {
			throw new Exception('QuestionSet: getLevelRead: '.$e->getMessage());
		}
	}

	public function get_id(){
		return $this->question_set_id;
	}

	public function get_name(){
		return $this->question_set_name;
	}

	public function get_description(){
		return $this->question_set_description;
	}

	public function get_level_read(){
		return $this->question_set_level_read;
	}

	public function get_level_write(){
		return $this->question_set_level_write;
	}

	public static function get_question_set_list(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$sql = "SELECT * FROM question_set";
		$stmt = $dbo->query($sql);
		return $stmt->fetchAll();
	}

	public static function get_interval_preset(){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = 'SELECT * FROM interval_preset';
			$stmt = $dbo->prepare($sql);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				return $stmt->fetchAll();
			}else{
				return null;
			}
		}catch(PDOException $e) {
			throw new Exception('QuestionSet: GetThumbnail: '.$e->getMessage());
		}
	}
}