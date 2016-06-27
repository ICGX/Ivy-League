<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.questionset.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';

$type = isset($_POST['type'])?$_POST['type']:null;

try{
	switch ($type) {
		case 'checkAnswer':
			require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.question.php';
			$uid = isset($_POST['uid'])?$_POST['uid']:null;
			if(!$uid) throw new Exception("No user id passed");
			$qid = isset($_POST['qid'])?$_POST['qid']:null;
			if(!$qid) throw new Exception("No question passed");
			$qtype = isset($_POST['qtype'])?$_POST['qtype']:null;
			if(!$qtype) throw new Exception("No question type passed");
			$q = new $qtype($qid);
			if(!$q) throw new Exception("Question not exist");
			if($q->attempt($uid, $_POST['answer'])){
				echo json_encode(array('status' => 'true'));
			}else{
				$qs = new QuestionSet($_POST['qsid']);
				//$qs->failed($uid);
				$errImg = array();
				foreach ($q->getImage() as $image) {
					// var_dump($image);
					if($image['type'] == 'error_image'){
						array_push($errImg, $image['id'].'.'.$image['ext']);
					}
				}
				echo json_encode(array('status' => 'false', 'image' => $errImg));
			}
			break;
		case 'signAWS':
			echo base64_encode(hash_hmac('sha1', $_POST['data'], '273RHkIgWkt1Fs4hOBC7zFxafzbqKKMu1oRW4/Vp', true));
			break;
		case 'watchLecture':
			require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.lecture.php';
			$uid = $_POST['uid'];
			$lid = $_POST['lid'];
			Lecture::attendLecture($uid, $lid);
			break;
		case 'schedule':
			$uid = $_POST['uid'];
			$user = new User($uid);
			$question_set = $user->get_calendar_questionset($_POST['timestamp']);
			$dateStr = date('Y-m-d', $_POST['timestamp']);
			echo $dateStr . "的練習:\n";
			if($question_set){
				foreach ($question_set as $id => $qs) {
					echo "{$qs['name']}科 - {$qs['qsname']}\n";
					// var_dump($qs);
				}
			}else{
				echo "當天沒有練習";
			}
			// $today_recommend = array();
			// $today_attempt = array();
			// $today_finished = array();
			// $today = time();
			// $date = $_POST['timestamp'];
			// $qs_list = QuestionSet::get_question_set_list();
			// $subjects = array();
			// foreach(User::get_subjects() as $s){
			// 	$subjects[$s['id']] = $s['name'];
			// }
			// foreach($qs_list as $qs){
			// 	$attempt = $user->get_attempt($qs['qsid']);
			// 	if(!$attempt) continue;
			// 	$attempt_date = date('Y-m-d', strtotime($attempt->started));
			// 	$lastsubmit_date = date('Y-m-d', strtotime($attempt->lastsubmit));
			// 	$ref_date = null;
			// 	if(!$lastsubmit_date || $lastsubmit_date == ''){
			// 		$ref_date = $attempt_date;
			// 	}else{
			// 		$ref_date = $lastsubmit_date;
			// 	}
			// 	if($ref_date == date("Y-m-d", $today)){
			// 		if($attempt->finish == "1"){
			// 			array_push($today_finished, $subjects[$qs['subject']] .' - '. $qs['name']);
			// 		}else{
			// 			array_push($today_attempt, $subjects[$qs['subject']] .' - '. $qs['name']);
			// 		}
			// 	}
			// 	if(count($today_finished) > 0){
			// 		echo "你今天完成了:\n";
			// 		echo implode("\n", $today_finished);
			// 		echo "\n";
			// 	}
			// 	if(count($today_attempt) > 0){
			// 		echo "你今天嘗試了:\n";
			// 		echo implode("\n", $today_attempt);
			// 	}
			// 	echo "\n";
			// 	echo date("Y-m-d", $date+86400)."的建議練習:";
			// }
			// $ts = new Date('Y-m-d', $date);
			// var_dump(date("Y-m-d", $_POST['timestamp']));
			break;
	}
} catch(Exception $e) {
	echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}