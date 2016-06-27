<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
$course_admin = new AccessControl('course_admin');
$user = new User($_SESSION['id']);
if(!$course_admin->hasReadAccess($user)){
	header('Location: /prohibited.php');
}
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.questionset.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.lecture.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';

// var_dump($_GET);
?>

<?php switch($_GET['type']): ?>
<?php case 'video': ?>
	<h2>上傳影片</h2>
	<form id="uploadVideo" action="<?php echo $_SERVER['PHP_SELF']; ?>?type=addVideo" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="location" value="<?php echo $_GET['key']; ?>">
		<input type="text" name="name" placeholder="影片名稱" required>
		<p>
			<select id="grade" name="grade">
				<option disabled selected value="null" required> -- 選擇年級 -- </option>
				<option value="1">一年級</option>
				<option value="2">二年級</option>
				<option value="3">三年級</option>
				<option value="4">四年級</option>
				<option value="5">五年級</option>
				<option value="6">六年級</option>
			</select>
		</p>
		<p>
			<select id="subject" name="subject">
				<option disabled selected value="null" required> -- 選擇科目 -- </option>
				<?php $permission = User::get_permission_list(); ?>
				<?php foreach(User::get_subjects() as $s) : ?>
					<option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php $prefixes = Lecture::get_prefix();?>
		<p>
			<select name="interval">
				<?php foreach($prefixes as $prefix): ?>
					<option value="<?php echo $prefix['id']; ?>">
						<?php echo ($prefix['isExam'] == 1?"考試安排：":"一般安排：");?>
						<?php echo $prefix['name']; ?>
						(+<?php echo $prefix['interval1'];?>,
						+<?php echo $prefix['interval2'];?>,
						+<?php echo $prefix['interval3'];?>,
						+<?php echo $prefix['interval4'];?>,
						+<?php echo $prefix['interval5'];?>)
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>預覽圖片: <input name="thumbnail_image" type="file"></p>
		<input type="submit" value="上載影片">
	</form>
	<script type="text/javascript">
		$('#uploadVideo').submit(function(e){
			console.log($('input[name=thumbnail_image]').val());
			if($('input[name=thumbnail_image]').val() == ""){
				alert("請上傳預覽圖片!");
				return false;
			}else if($('#subject option:selected').val() == "null"){
				alert("請選擇科目");
				return false;
			}else if($('#grade option:selected').val() == "null"){
				alert("請選擇年級");
				return false;
			}else{
				return true;
			}
		});
	</script>
<?php break; ?>
<?php case 'addVideo': ?>
<?php
	$lecture = new Lecture();
	$lecture->new_lecture($_POST['name'], $_POST['subject'], $_POST['grade'], $_POST['location'], $_POST['interval']);
	
	$target = $_SERVER['DOCUMENT_ROOT'].'/uploads/';
	if(isset($_FILES['thumbnail_image']) && $_FILES["thumbnail_image"]["error"] == 0){
		$ext = pathinfo($_FILES["thumbnail_image"]["name"],PATHINFO_EXTENSION);
		$img = $lecture->addImage('thumbnail_image', $ext);
		if($img)
			move_uploaded_file($_FILES["thumbnail_image"]["tmp_name"], $target.$img.'.'.$ext);
	}
?>
	<h2>影片已上載 － <?php echo $_POST['name']; ?></h2>
	<a href="/admin/index.php">返回</a>
<?php break; ?>
<?php endswitch; ?>

<?php require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php'; ?>
