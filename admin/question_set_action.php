<?php

session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.questionset.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.lecture.php';
$course_admin = new AccessControl('course_admin');
$user = new User($_SESSION['id']);
if(!$course_admin->hasReadAccess($user)){
	header('Location: /prohibited.php');
}

if(!isset($_POST['type'])){
	header('location: /admin/index.php');
}

// var_dump($_POST);
// var_dump($_SESSION);

switch ($_POST['type']) {
	case 'add':
		$l = new Lecture($_POST['lecture']);
		$profile = $l->getRelation();
		$lecture = new Lecture($profile->relation_id, $profile->relation_type);
		// var_dump($lecture);
		$relation_item = $lecture->getName();
		$qs = new QuestionSet(null, $_POST['name'], $_POST['permissionRead'], $_POST['permissionWrite'], $_SESSION['id'], $_POST['description'], $relation_item->subject, $relation_item->grade);
		// var_dump($_POST['lecture']);
		// $qs->relatedTo($);

		$l->idRelatedTo($qs->get_id());
		$target = $_SERVER['DOCUMENT_ROOT'].'/uploads/';
		if(isset($_FILES['thumbnail_image']) && $_FILES["thumbnail_image"]["error"] == 0){
			$ext = pathinfo($_FILES["thumbnail_image"]["name"],PATHINFO_EXTENSION);
			$img = $qs->addImage('thumbnail_image', $ext);
			if($img)
				move_uploaded_file($_FILES["thumbnail_image"]["tmp_name"], $target.$img.'.'.$ext);
		}
		?>
		<form action="question_set.php" id="form" method="POST">
			<input type="hidden" name="type" value="edit">
			<input type="hidden" name="qsid" value="<?php echo $qs->get_id(); ?>">
		</form>
		<script type="text/javascript">
			(function(){ document.getElementById("form").submit(); })();
		</script>
		<?php
		break;
	case 'remove':
		if($_POST['confirm'] == 'true'){
			$qs = new QuestionSet($_POST['qsid']);
			$qs->remove_question_set();
		}
		header('location: /admin/index.php');
	default:
		header('location: /admin/index.php');
		break;
}