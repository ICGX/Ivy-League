<?php

session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
$course_admin = new AccessControl('course_admin');
$user = new User($_SESSION['id']);
if(!$course_admin->hasReadAccess($user)){
	header('Location: /prohibited.php');
}

if(!isset($_POST['qsid']) || !isset($_POST['type'])) header('location: /admin/index.php');
// var_dump($_POST);

require_once '../models/class.question.php';

switch ($_POST['type']) {
	case 'add':
		// var_dump($_POST['description']);
		$target = $_SERVER['DOCUMENT_ROOT'].'/uploads/';
		if($_FILES["description_image"]["error"] == 0 && !getimagesize($_FILES["description_image"]["tmp_name"])){
		?>
		<form action="/admin/question.php" method="POST" id="redirect">
			<input type="hidden" name="type" value="add">
			<input type="hidden" name="error" value="Invalid image">
			<input type="hidden" name="qsid" value="<?php echo $_POST['qsid']; ?>">
		</form>
		<script type="text/javascript">
			document.getElementById('redirect').submit();
		</script>
		<?php
		}else if($_FILES["error_image"]["error"] == 0 && !getimagesize($_FILES["error_image"]["tmp_name"])){
		?>
		<form action="/admin/question.php" method="POST" id="redirect">
			<input type="hidden" name="type" value="add">
			<input type="hidden" name="error" value="Invalid image">
			<input type="hidden" name="qsid" value="<?php echo $_POST['qsid']; ?>">
		</form>
		<script type="text/javascript">
			document.getElementById('redirect').submit();
		</script>
		<?php
		}else{
			// var_dump(pathinfo($_FILES["error_image"]["name"],PATHINFO_EXTENSION));
			$q = $_POST['qtype']::newquestion($_POST['name'], $_POST['description'], $_POST['model_answer'], $_POST['answer'], $_POST['qsid']);
			if(isset($_FILES['description_image']) && $_FILES["description_image"]["error"] == 0){
				$ext = pathinfo($_FILES["description_image"]["name"],PATHINFO_EXTENSION);
				$img = $q->addImage('description_image', $ext);
				if($img)
					move_uploaded_file($_FILES["description_image"]["tmp_name"], $target.$img.'.'.$ext);
			}
			if(isset($_FILES['error_image']) && $_FILES["error_image"]["error"] == 0){
				$ext = pathinfo($_FILES["error_image"]["name"],PATHINFO_EXTENSION);
				$img = $q->addImage('error_image', $ext);
				if($img)
					move_uploaded_file($_FILES["error_image"]["tmp_name"], $target.$img.'.'.$ext);
			}
		}

		?>
		<form action="/admin/question_set.php" method="POST" id="redirect">
			<input type="hidden" name="type" value="edit">
			<input type="hidden" name="qsid" value="<?php echo $_POST['qsid']; ?>">
		</form>
		<script type="text/javascript">
			document.getElementById('redirect').submit();
		</script>
		<?php
		break;
	case 'remove':
		if($_POST['confirm'] == 'true'){
			$q = new $_POST['qtype']($_POST['qid']);
			$q->remove($_POST['qsid']);
			// var_dump($q);
		}
		?>
		<form action="/admin/question_set.php" method="POST" id="redirect">
			<input type="hidden" name="type" value="edit">
			<input type="hidden" name="qsid" value="<?php echo $_POST['qsid']; ?>">
		</form>
		<script type="text/javascript">
			document.getElementById('redirect').submit();
		</script>
		<?php
		break;
	default:
		header('location: /admin/index.php');
		break;
}