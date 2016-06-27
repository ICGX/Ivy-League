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
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.questionset.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.lecture.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';
?>
<div>
	<h2>溫習日期通知管理</h2>
	<form method="POST" action="admin_action.php">
		<input type="hidden" name="type" value="addTimeInterval">
		<span>新增通知時間</span>
		<p>名稱： <input type="text" name="name"></p>
		<p>請輸入時間間距:</p>
		<p>
			<input type="number" name="interval1" placeholder="第一個通知">
			<input type="number" name="interval2" placeholder="第二個通知">
			<input type="number" name="interval3" placeholder="第三個通知">
			<input type="number" name="interval4" placeholder="第四個通知">
			<input type="number" name="interval5" placeholder="第五個通知">
		</p>
		<p>
			<span>考試安排: </span>
			<select name="isExam">
				<option value="true">是</option>
				<option value="false" selected>否</option>
			</select>
		</p>
		<input type="submit" value="新增">
	</form>
	<h3>現有溫習日期通知</h3>
	<?php $presets = Lecture::get_prefix(); ?>
	<ul>
	<?php foreach($presets as $p): ?>
		<?php
		$t1 = $p['interval1'] + 1;
		$t2 = $t1 + $p['interval2'];
		$t3 = $t2 + $p['interval3'];
		$t4 = $t3 + $p['interval4'];
		$t5 = $t4 + $p['interval5'];
		?>
		<li><?php echo ($p['isExam']==1?"考試安排:":"一般安排:");?> +<?php echo $p['interval1'];?> (第<?php echo $t1; ?>日), +<?php echo $p['interval2'];?> (第<?php echo $t2; ?>日), +<?php echo $p['interval3'];?> (第<?php echo $t3; ?>日), +<?php echo $p['interval4'];?> (第<?php echo $t4; ?>日), +<?php echo $p['interval5'];?> (第<?php echo $t5; ?>日) </li>
	<?php endforeach; ?>
	</ul>
</div>
<hr>
<div>
	<h2>題目管理</h2>
	<form method="POST" action="question_set.php">
		<button type="submit" name="type" value="add">新增題目</button>
		
	</form>
</div>
<div>
	<?php
	$question_set_list = QuestionSet::get_question_set_list();
	$userLevel = $user->get_permission()->level;
	// var_dump($question_set_list);
	?>
	<form method="POST" action="question_set.php">
		<input type="hidden" name="type" value="edit">
		<h3>修改題目</h3>
		<select name="qsid">
			<?php foreach($question_set_list as $qs) : ?>
			<?php if($userLevel < $qs['levelWrite']) continue; ?>
			<option value="<?php echo $qs['qsid']; ?>">
				<?php echo $qs['name']; ?>
			</option>
			<?php endforeach; ?>
		</select>
		<button type="submit">修改</button>
	</form>
	
</div>
<div>
	<form method="POST" action="question_set.php">
		<input type="hidden" name="type" value="delete">
		<h3>移除題目</h3>
		<select name="qsid">
			<?php foreach($question_set_list as $qs) : ?>
			<option value="<?php echo $qs['qsid']; ?>">
				<?php echo $qs['name']; ?>
			</option>
			<?php endforeach; ?>
		</select>
		<button type="submit">移除</button>
	</form>
</div>
<hr>
<div>
	<h2>科目管理</h2>
	<form method="POST" action="admin_action.php">
		<p>
			<input type="text" name="subject" placeholder="科目名稱">
			<button type="submit" name="type" value="addSubject">新增科目</button>
		</p>
		<p>
			<select name="deleteSubject">
				<?php $subject = User::get_subjects(); ?>
				<?php foreach($subject as $s): ?>
					<option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
				<?php endforeach; ?>
			</select>
			<button type="submit" name="type" value="removeSubject">移除科目</button>
		</p>
	</form>
</div>
<hr>
<div>
	<h2>教學管理</h2>
	<h3>上載影片 (容量上限: <?php echo ini_get('upload_max_filesize'); ?>)</h3>
	<form id="UploadVideo" action="https://ivyleague.s3.amazonaws.com/" method="post" enctype="multipart/form-data">
		<input type="hidden" name="key" value="uploads/<?php echo time(); ?>.${filename}">
		<input type="hidden" name="AWSAccessKeyId" value="AKIAIRSFU3CZG734XLKQ"> 
		<input type="hidden" name="acl" value="public-read"> 
		<input type="hidden" name="success_action_redirect" value="http://54.169.205.65/redirect.php?type=video">
		<input type="hidden" name="policy" value="YOUR_POLICY_DOCUMENT_BASE64_ENCODED">
		<input type="hidden" name="signature" value="YOUR_CALCULATED_SIGNATURE">
		<input type="hidden" name="Content-Type" value="application/octet-stream">
		<!-- Include any additional input fields here -->
		<p><input name="file" type="file"></p>
		<p><input type="submit" value="上傳影片"></p>
	</form>
	<h3>上載圖片序列 (圖片容量上限: <?php echo ini_get('upload_max_filesize'); ?>)</h3>
	<form id="UploadImgSeq" action="/admin/admin_action.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="type" value="addImgSeq">
		<p><input type="text" name="name" placeholder="影片名稱"></p>
		<input id="count" type="hidden" name="count" value="1">
		<div id="imgSeq">
			<p><input class="img" name="img_0" type="file"><input name="interval_0" class="interval" type="number" placeholder="秒數" required><button class="addFile"> + </button></p>
		</div>
		<p>音樂: <input name="music" type="file"></p>
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
			<select name="interval">
				<?php $prefixes = Lecture::get_prefix();?>
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
		<p>
			<select id="subject" name="subject">
				<option disabled selected value="null" required> -- 選擇科目 -- </option>
				<?php $permission = User::get_permission_list(); ?>
				<?php foreach(User::get_subjects() as $s) : ?>
					<option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p><input type="submit" value="上傳圖片序列"></p>
		<script type="text/javascript">
			var count = 1;
			var addFileField = function(e){
				$('.addFile').off('click', addFileField);
				e.preventDefault();
				$('#imgSeq').append('<p><input name="img_'+count+'" type="file"><input name="interval_'+(count++)+'" class="interval" type="number" placeholder="秒數" required><button class="addFile"> + </button></p>');
				$('#count').val(count);
				$('.addFile').on('click', addFileField);
			};
			$('.addFile').on('click', addFileField);
			$('#UploadImgSeq').submit(function(){
				if($('.img').val() == ""){
					alert("請上傳所有圖片!");
					return false;
				}else if($('input[name=music]').val() == ""){
					alert("請上傳音樂!");
					return false;
				}else if($('#UploadImgSeq #grade option:selected').val()=='null'){
					alert("請選擇年級!");
					return false;
				}else if($('#UploadImgSeq #subject option:selected').val()=='null'){
					alert("請選擇科目!");
					return false;
				}else{
					return true;
				}
			});
		</script>
	</form>
	<h3>影片列表</h3>
	<form method="POST" action="/admin/admin_action.php">
		<p>
			<select name="subject">
				<?php $subject = User::get_subjects(); ?>
				<?php foreach($subject as $s): ?>
					<option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
				<?php endforeach; ?>
			</select>
			<select id="grade" name="grade">
				<option value="1">一年級</option>
				<option value="2">二年級</option>
				<option value="3">三年級</option>
				<option value="4">四年級</option>
				<option value="5">五年級</option>
				<option value="6">六年級</option>
			</select>
			<button type="submit" name="type" value="viewVideo">檢視影片</button>
		</p>
	</form>
	<h3>圖片序列列表</h3>
	<form method="POST" action="/admin/admin_action.php">
		<p>
			<select name="subject">
				<?php $subject = User::get_subjects(); ?>
				<?php foreach($subject as $s): ?>
					<option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
				<?php endforeach; ?>
			</select>
			<select id="grade" name="grade">
				<option value="1">一年級</option>
				<option value="2">二年級</option>
				<option value="3">三年級</option>
				<option value="4">四年級</option>
				<option value="5">五年級</option>
				<option value="6">六年級</option>
			</select>
			<button type="submit" name="type" value="viewSequence">檢視圖片序列</button>
		</p>
	</form>
</div>
<script type="text/javascript">
	$('#question_set').submit(function(){
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
	function pad(n, width, z) {
		z = z || '0';
		n = n + '';
		return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
	}
	var date = new Date();
	var expire = date.getFullYear();
	// date.setDate(date.getDate()+5);
	expire += '-';
	expire += pad(date.getUTCMonth()+1, 2);
	expire += '-';
	expire += pad(date.getUTCDate()+1, 2);
	expire += 'T';
	expire += pad(date.getUTCHours(), 2);
	expire += ':';
	expire += pad(date.getUTCMinutes(), 2);
	expire += ':';
	expire += pad(date.getUTCSeconds(), 2);
	expire += 'Z';
	console.log(expire);

	var policy = '{"expiration": "'+expire+'",';
  	policy += '"conditions": [ ';
    policy += '{"bucket": "ivyleague"}, ';
    policy += '["starts-with", "$key", "uploads/"],';
    policy += '{"acl": "public-read"},';
    policy += '{"success_action_redirect": "http://54.169.205.65/redirect.php?type=video"},';
    policy += '["starts-with", "$Content-Type", ""],';
    policy += '["content-length-range", 0, 1073741824]]}';
    $.ajax({
    	url: '/ajax.php',
    	type: 'POST',
    	data: {
    		type: 'signAWS',
    		data: window.btoa(policy)
    	}
    }).done(function(data){
    	$('input[name=signature]').val(data);
    	console.log(data);
    });
	$('input[name=policy]').val(window.btoa(policy));

	$('input[name=file]').change(function(){
		if(window.Blob){
			var file = $('input[name=file]')[0].files[0];
			var type = file.type;
			$('input[name=Content-Type]').val(type);
		}
	});
</script>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php'; ?>