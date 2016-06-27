<?php 
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
if(!isset($_SESSION['id'])){
	header('Location: /index.php');
}
$admin = new AccessControl('user_admin');
$user = new User($_SESSION['id']);
if(!$admin->hasReadAccess($user)){
	header('Location: /prohibited.php');
}
require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';
?>
<div>
	<?php $ip_list = AccessControl::getIPs(); ?>
	<h2>用戶管理</h2>
	<form action="user_action.php" method="POST">
		<p>
			<button name="type" value="add">新增用戶</button>
			<?php if($user->get_permission()->level == 5): ?>
				<button name="type" value="addAdmin">新增管理用戶</button>
			<?php endif; ?>
			<button name="type" value="addParent">新增家長</button>
		</p>
	</form>
	<p><input id="view_username" type="text" name="username"><button id="viewUser" name="type" value="view">檢視用戶</button></p>
	<p><input id="delete_username" type="text" name="username"><button id="deleteUser" name="type" value="delete">刪除用戶</button></p>
	<div id="user"></div>
</div>
<script type="text/javascript">
	// var permission = ['']
	<?php
		$permissions = array();
		$level_list = array();
		foreach (User::get_permission_list() as $level) {
			$permissions[$level['level']] = $level['name'];
			array_push($level_list, $level['name']);
		}
	?>
	var permission = ['<?php echo implode("','", $level_list); ?>'];
	$('#viewUser').click(function(){
		$.ajax({
			url: 'user_action.php',
			type: 'POST',
			data: {type: 'viewUser', username: $('#view_username').val()}
		}).done(function(data){
			var grade = ['','一年級','二年級','三年級','四年級','五年級','六年級'];
			// console.log(data);
			var profile = JSON.parse(data);
			// console.log(profile);
			var subject = [];
			for(var i in profile.subject){
				subject.push(profile.subject[i].name);
			}
			$('#user').html('');
			if(profile.user){
				$("#user").append('<p>用戶名: '+profile.user.username+'</p>');
				$("#user").append('<p>姓名: '+profile.user.name+'</p>');
				var sex = (profile.user.sex == 'nam'?'男':'女');
				$("#user").append('<p>性別: '+sex+'</p>');
				$("#user").append('<p>出生日期: '+profile.user.borndate+'</p>');
				if(profile.user.level == 2) $("#user").append('<p>年級: '+grade[profile.user.grade]+'</p>');
				if(profile.user.level == 2) $("#user").append('<p>學科: '+subject.join(', ')+'</p>');
				$("#user").append('<p>電話: '+profile.user.telephone+'</p>');
				$("#user").append('<p>地址: '+profile.user.address+'</p>');
				$("#user").append('<p>電郵: '+profile.user.email+'</p>');
				$("#user").append('<p>權限: '+permission[profile.user.level]+'</p>');
				if(profile.parent){
					$("#user").append('<p>家長姓名: '+profile.parent.name+'</p>');
					$("#user").append('<p>家長電郵: '+profile.parent.email+'</p>');
				}
				if(profile.children && profile.children.length > 0){
					for(var i in profile.children){
						$("#user").append('<blockquote><h4>子女姓名: '+profile.children[i].name+'</h4><p>子女用戶名: '+profile.children[i].username+'</p></blockquote>');
					}
				}
			}else{
				$("#user").append('<h3>用戶不存在!</h3>');
			}
		});
	});
	$('#deleteUser').click(function(e){
		$.ajax({
			url: 'user_action.php',
			type: 'POST',
			data: {type: 'deleteUser', username: $('#delete_username').val()}
		}).done(function(data){
			// console.log(data);
			var result = JSON.parse(data);
			if(result.status == 'true'){
				alert("用戶已刪除");
			}else{
				var message = '用戶不存在';
				if(result.message) message = result.message;
				alert("用戶刪除錯誤: " + message);
			}
		});
	});
</script>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php'; ?>