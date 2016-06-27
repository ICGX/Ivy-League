<?php 
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
if(!isset($_SESSION['id'])){
	header('Location: /index.php');
}
$course_admin = new AccessControl('course_admin');
$user = new User($_SESSION['id']);
if(!$course_admin->hasReadAccess($user)){
	header('Location: /prohibited.php');
}
if(!isset($_POST['type'])) header('location: /admin/index.php');

require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.question.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';
?>

<?php switch($_POST['type']): ?>
<?php case 'add': ?>
<?php if(isset($_POST['error'])): ?>
	<h3 style="color: red;"><?php echo $_POST['error']; ?></h3>
<?php endif; ?>
<h2>新增子題目</h2>
<select id="question_type" required>
	<option disabled selected value="null"> -- 選擇子題目類型 -- </option>
<?php foreach(Question::get_types() as $typeid => $type): ?>
	<option value="<?php echo $typeid; ?>"><?php echo $type; ?></option>
<?php endforeach; ?>
</select>
<div>
	<form id="question" action="/admin/question_action.php" method="POST" enctype="multipart/form-data"></form>
</div>
<script type="text/javascript">
	$('#question_type').change(function(){
		$.get('/views/admin/question.'+$('#question_type option:selected').val()+'.php',
			function(data){
				$('#question').html('<input type="hidden" name="qsid" value="<?php echo $_POST['qsid'];?>">');
				$('#question').append('<input type="hidden" name="type" value="add">');
				$('#question').append(data);
				$('#question').append('<input type="submit" value="新增">');
			});
	});
</script>
<?php break; ?>
<?php case 'remove': ?>
<?php
$q = new $_POST['qtype']($_POST['qid']);
?>
<form id="question" action="/admin/question_action.php" method="POST">
	<input type="hidden" name="type" value="remove">
	<input type="hidden" name="qsid" value="<?php echo $_POST['qsid']; ?>">
	<input type="hidden" name="qtype" value="<?php echo $q->get_type(); ?>">
	<input type="hidden" name="qid" value="<?php echo $q->get_id(); ?>">
	<h2>刪除子題目</h2>
	<p>你確認要刪除題目 <b><?php echo $q->getName();?></b> 嗎?</p>
	<p><?php echo $q->getQuestion(); ?></p>
	<p><button type="submit" name="confirm" value="true">確定</button><button type="submit" name="confirm" value="false">取消</button></p>
</form>
<?php break; ?>
<?php case 'view': ?>
<?php
$types = Question::get_types();
$q = new $_POST['qtype']($_POST['qid']);
?>
<form action="/admin/question_set.php" method="POST" id="redirect">
	<h2><?php echo $types[$q->get_type()]; ?> - 題目: <b><?php echo $q->getName();?></b></h2>
	<?php include $_SERVER['DOCUMENT_ROOT'].'/views/admin/question.'.$q->get_type().'.answerview.php'; ?>
	<input type="hidden" name="type" value="edit">
	<input type="hidden" name="qsid" value="<?php echo $_POST['qsid']; ?>">
	<p><button type="submit">返回</button></p>
</form>
<?php endswitch; ?>
<?php include $_SERVER['DOCUMENT_ROOT'].'/views/footer.php'; ?>
