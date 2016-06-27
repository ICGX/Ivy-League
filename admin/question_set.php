<?php 
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.questionset.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.lecture.php';
if(!isset($_SESSION['id'])){
	header('Location: /index.php');
}
$course_admin = new AccessControl('course_admin');
$user = new User($_SESSION['id']);
if(!$course_admin->hasReadAccess($user)){
	header('Location: /prohibited.php');
}
require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';
?>

<?php if(!isset($_POST['type'])): ?>
	<script type="text/javascript">
		window.location = '/';
	</script>
<?php endif; ?>
<?php switch($_POST['type']): ?>
<?php case 'add': ?>
	<form action="/admin/question_set_action.php" method="POST" id="question_set" enctype="multipart/form-data">
		<input type="hidden" name="type" value="add">
		<h2>新增題目</h2>
		<div>
			<input id="name" type="text" name="name" placeholder="題目名稱" required>
			<p>
				<select id="permissionRead" name="permissionRead">
					<option disabled selected value="null" required> -- 選擇最低使用權限 -- </option>
					<?php $permission = User::get_permission_list(); ?>
					<?php foreach($permission as $p) : ?>
						<option value="<?php echo $p['level']; ?>"><?php echo $p['name']; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<select id="permissionWrite" name="permissionWrite">
					<option disabled selected value="null" required> -- 選擇最低修改權限 -- </option>
					<?php $permission = User::get_permission_list(); ?>
					<?php foreach($permission as $p) : ?>
						<option value="<?php echo $p['level']; ?>"><?php echo $p['name']; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p>
				<textarea name="description" placeholder="註解"></textarea>
			</p>
			<p>
				<span>選擇教學資料:</span>
				<select name="lecture">
					<?php $lectures = Lecture::getAllLectures(); ?>
					<?php foreach($lectures as $lecture): ?>
						<?php $l = new Lecture($lecture['relation_id'], $lecture['relation_type']); ?>
						<?php $name = $l->getName(); ?>
						<option value="<?php echo $lecture['id'];?>"><?php if($name) echo $name->name; ?></option>
					<?php endforeach;?>
				</select>
			</p>
			<p>
				預覽圖片: <p><input name="thumbnail_image" type="file"></p>
			</p>
			<button id="submit" type="submit">新增</button>
		</div>
	</form>
	<script type="text/javascript">
		var validation = function() {
			if ($('#name').val().length > 0) {
				$('#submit').attr('disabled', false);
			}else{
				$('#submit').attr('disabled', true);
			}
		}
		$(document).ready(validation);
		$('#name').on('input',validation);
		$('#question_set').submit(function(){
			if($('#name').val().length <= 0){
				alert("請輸入題目");
				return false;
			}else if($('#permissionRead option:selected').val() == "null"
				|| $('#permissionWrite option:selected').val() == "null" ){
				alert("請選擇權限");
				return false;
			}else if($('#subject option:selected').val() == "null"){
				alert("請選擇科目");
				return false;
			}else if($('#grade option:selected').val() == "null"){
				alert("請選擇年級");
				return false;
			}else if($('input[name=thumbnail_image]').val() == ""){
				alert("請上載預覽圖片");
				return false;
			}else{
				return true;
			}
		});
	</script>
<?php break; ?>
<?php case 'edit': ?>
	<?php
	$qs = new QuestionSet($_POST['qsid']);
	$p_read = User::get_permission_by_level($qs->get_level_read());
	$p_write = User::get_permission_by_level($qs->get_level_write());
	$subjects = array();
	foreach (User::get_subjects() as $s) {
		$subjects[$s['id']] = $s['name'];
	}
	$grades = array('','一年級','二年級','三年級','四年級','五年級','六年級');
	?>
	<input type="hidden" name="type" value="add">
	<?php if(isset($_POST['error'])): ?>
		<h3 style="color: red;"><?php echo $_POST['error']; ?></h3>
	<?php endif; ?>
	<h2>修改題目 - <?php echo $grades[$qs->get_grade()->grade];?> - <?php echo $subjects[$qs->get_subject()->subject];?> - <?php echo $qs->get_name();?> (最低使用權限: <?php echo $p_read->name;?>, 最低修改權限: <?php echo $p_write->name;?> )</h2>
	<?php $thumbnail = $qs->getThumbnail(); ?>
	<?php if($thumbnail): ?>
		<p>預覽圖片:</p>
		<img style="max-width: 100%; max-height: 300px;" src="/uploads/<?php echo $thumbnail->id;?>.<?php echo $thumbnail->ext;?>" alt="thumb">
	<?php endif; ?>
	<?php 
	$description = $qs->get_description();
	if($description && strlen($description) > 0):
	?>
		<p>註解:</p>
		<blockquote><?php echo $description; ?></blockquote>
	<?php endif; ?>
	<div>
		<form action="question.php" method="POST">
			<input type="hidden" name="qsid" value="<?php echo $qs->get_id();?>">
			<button type="submit" name="type" value="add">新增子題目</button>
		</form>
	</div>
	<?php foreach($qs->get_questions() as $q): ?>
		<div>
			<h3><?php echo Question::get_types($q->get_type());?> - <?php echo $q->getName(); ?></h3>
			<blockquote>
				<p><?php echo $q->getQuestion(); ?></p>
			</blockquote>
			<form action="question.php" method="POST">
				<input type="hidden" name="qsid" value="<?php echo $qs->get_id();?>">
				<input type="hidden" name="qid" value="<?php echo $q->get_id();?>">
				<input type="hidden" name="qtype" value="<?php echo $q->get_type();?>">
				<button type="submit" name="type" value="view">檢視子題目</button>
				<button type="submit" name="type" value="remove">刪除子題目</button>
			</form>
		</div>
	<?php endforeach; ?>
<?php break; ?>
<?php case 'delete': ?>
	<?php
	$types = Question::get_types();
	$qs = new QuestionSet($_POST['qsid']);
	$q = $qs->get_questions();
	?>
	<h2>你確定要移除 <?php echo $qs->get_name(); ?> 嗎？</h2>
	<p>此題目內有下列子題目:</p>
	<ul>
	<?php foreach($q as $q) : ?>
		<li><?php echo $types[$q->get_type()]; ?> - <?php echo $q->get_name(); ?></li>
	<?php endforeach; ?>
	</ul>
	<form action="/admin/question_set_action.php" method="POST">
		<input type="hidden" name="type" value="remove">
		<input type="hidden" name="qsid" value="<?php echo $qs->get_id();?>">
		<button type="submit" name="confirm" value="true">移除</button>
		<button type="submit" name="confirm" value="false">取消</button>
	</form>
<?php break; ?>
<?php endswitch; ?>
<?php include $_SERVER['DOCUMENT_ROOT'].'/views/footer.php'; ?>