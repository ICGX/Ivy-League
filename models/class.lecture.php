<?php
require_once 'class.database.php';

class Lecture
{
	private $db;
	private $dbo;
	private $id;
	private $isVideo;
	private $lid;
	private $type;

	function __construct($id = null, $type = null){
		$this->db = new DATABASE();
		$this->dbo = $this->db->getDBO();
		if($id){
			$this->id = $id;
			$sql = "SELECT * FROM lecture WHERE $id = ?";
			$stmt = $this->dbo->prepare($sql);
			$stmt->execute(array($id));
			if($stmt->rowCount() > 0){
				$l = $stmt->fetch(PDO::FETCH_OBJ);
				$this->type = $l->relation_type;
				$this->lid = $l->id;
				if($this->type == 'video')
					$this->isVideo = 1;
				else
					$this->isVideo = 0;
			}

		}

		if($type){
			$this->type = $type;
			if($type == 'video'){
				$this->isVideo = true;
			}else{
				$this->isVideo = false;
			}
		}else{
			$this->isVideo = true;
		}
	}

	public function isVideo(){
		return $this->isVideo();
	}

	public function new_lecture($name, $subject, $grade, $location, $preset){
		$this->dbo->beginTransaction();
		try{
			$sql = "INSERT INTO video(name, subject, grade, location) VALUES (:name, :subject, :grade, :location)";
			$stmt = $this->dbo->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':subject', $subject);
			$stmt->bindParam(':grade', $grade);
			$stmt->bindParam(':location', $location);
			$stmt->execute();
			$this->id = $this->dbo->lastInsertId();
			$sql = "INSERT INTO lecture(relation_type, relation_id, preset_id) VALUES ('video', :relation_id, :preset_id)";
			$stmt = $this->dbo->prepare($sql);
			$stmt->bindParam(':relation_id', $this->id);
			$stmt->bindParam(':preset_id', $preset);
			$stmt->execute();
			$this->lid = $this->dbo->lastInsertId();
			$this->dbo->commit();
		} catch(PDOException $e) {
			$stmt->rollback();
			throw new Exception('Lecture: NewVideo: '.$e->getMessage());
		}
	}

	public function new_lecture_sequence($name, $subject, $grade, $preset){
		$this->dbo->beginTransaction();
		try{
			$sql = "INSERT INTO image_sequence(name, subject, grade) VALUES (:name, :subject, :grade)";
			$stmt = $this->dbo->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':subject', $subject);
			$stmt->bindParam(':grade', $grade);
			$stmt->execute();

			$this->isVideo = false;
			$this->id = $this->dbo->lastInsertId();
			$sql = "INSERT INTO lecture(relation_type, relation_id, preset_id) VALUES ('sequence', :relation_id, :preset_id)";
			$stmt = $this->dbo->prepare($sql);
			$stmt->bindParam(':relation_id', $this->id);
			$stmt->bindParam(':preset_id', $preset);
			$stmt->execute();
			$this->lid = $this->dbo->lastInsertId();
			$this->dbo->commit();
		} catch(PDOException $e) {
			$stmt->rollback();
			throw new Exception('Lecture: NewSequence: '.$e->getMessage());
		}
	}

	public function addImage($type, $ext){
		$this->dbo->beginTransaction();
		try{
			$sql = "INSERT INTO image(type, relation, relation_id, ext) VALUES (?,?,?,?)";
			$stmt = $this->dbo->prepare($sql);
			$stmt->execute(array($type, 'Lecture', $this->id, $ext));
			$id = $this->dbo->lastInsertId('id');
			$this->dbo->commit();
			return $id;
		} catch(PDOException $e) {
			$this->dbo->rollback();
			throw new Exception('Lecture: DB: AddImage: '.$e->getMessage());
		}
		return null;
	}

	public function getSequence(){
		try{
			$result = array();
			$sql = 'SELECT * FROM image WHERE type = "sequence_image" AND relation_id = :seqid ORDER BY id';
			$stmt = $this->dbo->prepare($sql);
			$stmt->bindParam(':seqid', $this->id);
			$stmt->execute();
			if($stmt->rowCount() <= 0) return null;
			$result['images'] = $stmt->fetchAll();
			$sql = 'SELECT * FROM music WHERE seqid = :seqid';
			$stmt = $this->dbo->prepare($sql);
			$stmt->bindParam(':seqid', $this->id);
			$stmt->execute();
			if($stmt->rowCount() <= 0) return null;
			$result['music'] = $stmt->fetch(PDO::FETCH_OBJ);
			return $result;
		}catch(PDOException $e) {
			throw new Exception('Lecture: GetVideo: '.$e->getMessage());
		}
	}

	public function refreshLid($idIsRelId = false){
		if($idIsRelId){
			$sql = "SELECT * FROM lecture WHERE relation_id = ?";
			$stmt = $this->dbo->prepare($sql);
			$stmt->execute(array($this->id));
			if($stmt->rowCount() > 0){
				$l = $stmt->fetch(PDO::FETCH_OBJ);
				$this->lid = $l->id;
			}
		}else{
			$sql = "SELECT * FROM lecture WHERE id = ?";
			$stmt = $this->dbo->prepare($sql);
			$stmt->execute(array($this->id));
			if($stmt->rowCount() > 0){
				$l = $stmt->fetch(PDO::FETCH_OBJ);
				$this->lid = $l->relation_id;
			}
		}
	}

	public function getLectureId(){
		if($this->isVideo){
			$sql = "SELECT * FROM lecture WHERE id = ?";
			$stmt = $this->dbo->prepare($sql);
			$stmt->execute(array($this->lid));
			if($stmt->rowCount() > 0){
				$l = $stmt->fetch(PDO::FETCH_OBJ);
				return $l->id;
			}
		}
		return $this->lid;
	}

	public function getId(){
		return $this->id;
	}

	public function addMusic($ext){
		$this->dbo->beginTransaction();
		try{
			$sql = "INSERT INTO music(seqid, ext) VALUES (?,?)";
			$stmt = $this->dbo->prepare($sql);
			$stmt->execute(array($this->id, $ext));
			$id = $this->dbo->lastInsertId('id');
			$this->dbo->commit();
			return $id;
		} catch(PDOException $e) {
			$this->dbo->rollback();
			throw new Exception('Lecture: DB: AddImage: '.$e->getMessage());
		}
		return null;
	}

	public function getMusic(){
		try{
			$sql = 'SELECT * FROM music WHERE seqid = :seqid';
			$stmt = $this->dbo->prepare($sql);
			$stmt->bindParam(':seqid', $this->id);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				return $stmt->fetch(PDO::FETCH_OBJ);
			}else{
				return null;
			}
		}catch(PDOException $e) {
			throw new Exception('Lecture: GetVideo: '.$e->getMessage());
		}
	}

	public function getVideo(){
		try{
			$sql = 'SELECT * FROM video WHERE vid = :vid';
			$stmt = $this->dbo->prepare($sql);
			$stmt->bindParam(':vid', $this->id);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				return $stmt->fetch(PDO::FETCH_OBJ);
			}else{
				return null;
			}
		}catch(PDOException $e) {
			throw new Exception('Lecture: GetVideo: '.$e->getMessage());
		}
	}

	public function getThumbnail() {
		if($this->isVideo){
			try{
				$sql = 'SELECT * FROM image WHERE relation = "Lecture" AND type = "thumbnail_image" AND relation_id = :vid';
				$stmt = $this->dbo->prepare($sql);
				$stmt->bindParam(':vid', $this->id);
				$stmt->execute();
				if($stmt->rowCount() > 0){
					return $stmt->fetch(PDO::FETCH_OBJ);
				}else{
					return null;
				}
			}catch(PDOException $e) {
				throw new Exception('Lecture: GetThumbnail: '.$e->getMessage());
			}
		}else{
			try{
				$sql = 'SELECT * FROM image WHERE relation = "Lecture" AND type = "sequence_image" AND relation_id = :id ORDER BY id ASC';
				$stmt = $this->dbo->prepare($sql);
				$stmt->bindParam(':id', $this->id);
				$stmt->execute();
				if($stmt->rowCount() > 0){
					return $stmt->fetch(PDO::FETCH_OBJ);
				}else{
					return null;
				}
			}catch(PDOException $e) {
				throw new Exception('Lecture: GetThumbnail: '.$e->getMessage());
			}
		}
	}

	public function relatedTo($qsid){
		$this->dbo->beginTransaction();
		try{
			$sql = "INSERT INTO lecture_revision(lid, relation_id) VALUES (?,?)";
			$stmt = $this->dbo->prepare($sql);
			$stmt->execute(array($this->lid, $qsid));
			$this->dbo->commit();
		}catch(PDOException $e){
			$this->dbo->rollback();
			throw new Exception('Lecture: RelatedTo: '.$e->getMessage());
		}
	}

	public function idRelatedTo($qsid){
		$this->dbo->beginTransaction();
		try{
			$sql = "INSERT INTO lecture_revision(lid, relation_id) VALUES (?,?)";
			$stmt = $this->dbo->prepare($sql);
			$stmt->execute(array($this->id, $qsid));
			$this->dbo->commit();
		}catch(PDOException $e){
			$this->dbo->rollback();
			throw new Exception('Lecture: RelatedTo: '.$e->getMessage());
		}
	}

	public function updateSeqInterval($interval){
		$this->dbo->beginTransaction();
		try{
			$sql = "UPDATE image_sequence SET `interval` = :interval WHERE id = :id";
			$stmt = $this->dbo->prepare($sql);
			$stmt->bindParam(':interval', $interval);
			$stmt->bindParam(':id', $this->id);
			$stmt->execute();
			$this->dbo->commit();
		}catch(PDOException $e){
			$this->dbo->rollback();
			throw new Exception('Lecture: RelatedTo: '.$e->getMessage());
		}
	}

	public function getRelation(){
		$sql = 'SELECT relation_id, relation_type FROM lecture WHERE id = ?';
		$stmt = $this->dbo->prepare($sql);
		$stmt->execute(array($this->id));
		if($stmt->rowCount() <= 0)
			return null;
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

	public function getRevision(){
		$sql = 'SELECT * FROM lecture_revision WHERE lid = ?';
		$stmt = $this->dbo->prepare($sql);
		$stmt->execute(array($this->lid));
		if($stmt->rowCount() <= 0)
			return null;
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getName(){
		$sql = '';
		if($this->isVideo == 1)
			$sql = 'SELECT * FROM video WHERE vid = ?';
		else
			$sql = 'SELECT * FROM image_sequence WHERE id = ?';
		try{
			$stmt = $this->dbo->prepare($sql);
			$stmt->execute(array($this->id));
			if($stmt->rowCount() > 0)
				return $stmt->fetch(PDO::FETCH_OBJ);
			else
				return null;
		}catch(PDOException $e){
			throw new Exception('Lecture: getName: '.$e->getMessage());
		}
	}

	public function get_type(){
		return $this->type;
	}

	public function remove(){
		$this->dbo->beginTransaction();
		if($this->isVideo){
			try{
				$sql = "SELECT * FROM image WHERE relation = 'Lecture' AND relation_id = ?";
				$stmt = $this->dbo->prepare($sql);
				$stmt->execute(array($this->id));
				$thumbnails = $stmt->fetchAll();
				$sql = "DELETE FROM image WHERE relation = 'Lecture' AND relation_id = :vid";
				$stmt = $this->dbo->prepare($sql);
				$stmt->bindParam(":vid", $this->id);
				$stmt->execute();
				$sql = "DELETE FROM video WHERE vid = :vid";
				$stmt = $this->dbo->prepare($sql);
				$stmt->bindParam(':vid', $this->id);
				$stmt->execute();
				$this->dbo->commit();
				foreach ($thumbnails as $image) {
					if(file_exists($_SERVER['DOCUMENT_ROOT'].'/uploads/'.$image['id'].'.'.$image['ext']))
						unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/'.$image['id'].'.'.$image['ext']);
				}
			} catch(PDOException $e) {
				$this->dbo->rollback();
				throw new Exception('Lecture: RemoveVideo: '.$e->getMessage());
			}
		}else{
			try{
				$sql = "SELECT * FROM image WHERE relation = 'Lecture' AND type = 'sequence_image' AND relation_id = ?";
				$stmt = $this->dbo->prepare($sql);
				$stmt->execute(array($this->id));
				$thumbnails = $stmt->fetchAll();
				$sql = "SELECT * FROM music WHERE seqid = ?";
				$stmt = $this->dbo->prepare($sql);
				$stmt->execute(array($this->id));
				$music = $stmt->fetch(PDO::FETCH_OBJ);
				$sql = "DELETE FROM image WHERE relation = 'Lecture' AND relation_id = :vid";
				$stmt = $this->dbo->prepare($sql);
				$stmt->bindParam(":vid", $this->id);
				$stmt->execute();
				$sql = "DELETE FROM music WHERE seqid = :vid";
				$stmt = $this->dbo->prepare($sql);
				$stmt->bindParam(':vid', $this->id);
				$stmt->execute();
				$sql = "DELETE FROM image_sequence WHERE id = :vid";
				$stmt = $this->dbo->prepare($sql);
				$stmt->bindParam(':vid', $this->id);
				$stmt->execute();
				$this->dbo->commit();
				foreach ($thumbnails as $image) {
					if(file_exists($_SERVER['DOCUMENT_ROOT'].'/uploads/'.$image['id'].'.'.$image['ext']))
						unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/'.$image['id'].'.'.$image['ext']);
				}
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/uploads/m.'.$music->id.'.'.$music->ext))
					unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/m.'.$music->id.'.'.$music->ext);
			} catch(PDOException $e) {
				$this->dbo->rollback();
				throw new Exception('Lecture: RemoveSequence: '.$e->getMessage());
			}
		}
		return null;
	}

	public static function getVideoBySubjectGrade($subject, $grade){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM video WHERE subject = :subject AND grade = :grade";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':subject', $subject);
			$stmt->bindParam(':grade', $grade);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				return $stmt->fetchAll();
			}else{
				return null;
			}
		}catch(PDOException $e) {
			throw new Exception('Lecture: RemoveSet: '.$e->getMessage());
		}
	}

	public static function getSequenceBySubjectGrade($subject, $grade){
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM image_sequence WHERE subject = :subject AND grade = :grade";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':subject', $subject);
			$stmt->bindParam(':grade', $grade);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				return $stmt->fetchAll();
			}else{
				return null;
			}
		}catch(PDOException $e) {
			throw new Exception('Lecture: RemoveSet: '.$e->getMessage());
		}
	}

	public static function getBaseUrl() {
		return 'http://ivyleague.s3.amazonaws.com/';
	}

	public static function attendLecture($uid, $lid) {
		require_once 'class.questionset.php';
		$db = new DATABASE();
		$dbo = $db->getDBO();
		$dbo->beginTransaction();
		try{
			$sql = "SELECT * FROM `lecture_revision` WHERE lid = :lid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':lid', $lid);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				// $all = $stmt->fetchAll();
				$sql = "SELECT l.id, l.relation_type, lr.relation_id, ip.* FROM `lecture` l LEFT JOIN lecture_revision lr ON l.id = lr.lid LEFT JOIN interval_preset ip ON l.preset_id = ip.id WHERE l.id = :lid";
				$stmt = $dbo->prepare($sql);
				$stmt->bindParam(':lid', $lid);
				$stmt->execute();
				$today = strtotime(date('Y-m-d', time()));
				$oneday = 86400;
				$lectures = $stmt->fetchAll();

				$presql = "SELECT * FROM calendar WHERE uid = :uid AND lid = :lid AND qsid = :qsid";
				$prestmt = $dbo->prepare($presql);
				$sql = "INSERT INTO calendar(uid, lid, qsid, `timestamp`) VALUES (:uid, :lid,:qsid,FROM_UNIXTIME(:ts))";
				$stmt = $dbo->prepare($sql);

				$qs = self::getQustionSetByLectureId($lid);
				foreach($lectures as $l){
					$accumulate = $today;
					$prestmt->bindParam(":uid", $uid);
					$prestmt->bindParam(":lid", $lid);
					$prestmt->bindParam(":qsid", $l['relation_id']);
					$prestmt->execute();
					if($prestmt->rowCount() > 0) continue;

					for($i = 1; $i <= 5; $i++){
						if($l["interval".$i]){
							// echo "+ ".$l["interval".$i]."\n";
							$accumulate += $l["interval".$i] * $oneday;
							$stmt->bindParam(":uid", $uid);
							$stmt->bindParam(":lid", $lid);
							$stmt->bindParam(":qsid", $l['relation_id']);
							$stmt->bindParam(":ts", $accumulate);
							// echo "PUSH ".$lid." qsid: ".$l['relation_id']. " ts: ".($accumulate);
							$stmt->execute();
						}
					}
				}
			}
			$dbo->commit();
		}catch(PDOException $e){
			$dbo->rollback();
			throw new Exception("Lecture: attendLecture: ".$e->getMessage());
		}
	}

	public static function getQustionSetByLectureId($lid) {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM lecture_revision WHERE lid = :lid";
			$stmt = $dbo->prepare($sql);
			$stmt->bindParam(':lid', $lid);
			$stmt->execute();
			if($stmt->rowCount() > 0)
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			else
				return null;
		}catch(PDOException $e){
			throw new Exception("Lecture: GetQuestionByLID: ".$e->getMessage());
		}
	}

	public static function getLectures() {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM video";
			$stmt = $dbo->prepare($sql);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				return $stmt->fetchAll();
			}else{
				return null;
			}
		}catch(PDOException $e) {
			throw new Exception('Lecture: GetLectures: '.$e->getMessage());
		}
	}

	public static function getAllLectures() {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM lecture";
			$stmt = $dbo->prepare($sql);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				return $stmt->fetchAll();
			}else{
				return null;
			}
		}catch(PDOException $e) {
			throw new Exception('Lecture: GetLectures: '.$e->getMessage());
		}
	}

	public static function get_prefix() {
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "SELECT * FROM interval_preset";
			$stmt = $dbo->prepare($sql);
			$stmt->execute();
			if($stmt->rowCount() > 0){
				return $stmt->fetchAll();
			}else{
				return null;
			}
		}catch(PDOException $e) {
			throw new Exception('Lecture: Prefix: '.$e->getMessage());
		}
	}

	function __destruct(){
		$db = null;
	}
}