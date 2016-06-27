<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
$course_admin = new AccessControl('course_admin');
$user = new User($_SESSION['id']);
if(!$course_admin->hasReadAccess($user)){
	header('Location: /prohibited.php');
}

switch ($_POST['type']) {
	case 'addSubject':
		User::add_subject($_POST['subject']);
		break;
	case 'removeSubject':
		User::remove_subject($_POST['deleteSubject']);
		break;
	case 'viewVideo':
		include $_SERVER['DOCUMENT_ROOT'].'/views/admin/lecture.video.php';
		return;
	case 'viewSequence':
		include $_SERVER['DOCUMENT_ROOT'].'/views/admin/lecture.sequence.php';
		return;
	case 'removeVideo':
		require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.lecture.php';
		$lecture = new Lecture($_POST['id']);
		$lecture->remove();
		break;
	case 'removeSequence':
		require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.lecture.php';
		$lecture = new Lecture($_POST['id'], 'sequence');
		$lecture->remove();
		break;
	case 'addImgSeq':
		require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.lecture.php';
		$target = $_SERVER['DOCUMENT_ROOT'].'/uploads/';
		$lecture = new Lecture();
		$lecture->new_lecture_sequence($_POST['name'], $_POST['subject'], $_POST['grade'], $_POST['interval']);
		$seqInterval = array();
		for($i = 0; $i < $_POST['count']; $i++){
			$file = $_FILES['img_'.$i];
			if(isset($file) && $file["error"] == 0){
				$ext = pathinfo($file["name"],PATHINFO_EXTENSION);
				$img = $lecture->addImage('sequence_image', $ext);
				array_push($seqInterval, $_POST['interval_'.$i]);
				if($img)
					move_uploaded_file($file["tmp_name"], $target.$img.'.'.$ext);
			}
		}
		$lecture->updateSeqInterval(implode('`', $seqInterval));
		if(isset($_FILES['music']) && $_FILES['music']["error"] == 0){
			$ext = pathinfo($_FILES['music']["name"],PATHINFO_EXTENSION);
			$music = $lecture->addMusic($ext);
			if($music)
				move_uploaded_file($_FILES['music']["tmp_name"], $target.'m.'.$music.'.'.$ext);
		}
		break;
	case 'addTimeInterval':
		require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.database.php';
		$db = new DATABASE();
		$dbo = $db->getDBO();
		try{
			$sql = "INSERT INTO interval_preset(interval1, interval2, interval3, interval4, interval5, name, isExam) VALUES (?,?,?,?,?,?,".($_POST['isExam'] == 'true'?"1":"0").")";
			$stmt = $dbo->prepare($sql);
			$stmt->execute(array($_POST['interval1'], $_POST['interval2'], $_POST['interval3'], $_POST['interval4'], $_POST['interval5'], $_['name']));
		}catch(PDOException $e){
			throw new Exception('AddTimeInterval: '.$e->getMessage());
		}
		break;
}
header('Location:/admin/index.php');