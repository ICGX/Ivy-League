<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
$ip = $_SERVER['REMOTE_ADDR'];
if(!AccessControl::checkIP($ip)){
	header('Location: /prohibited.php');
}
$curtime = time();
// echo $_SESSION['timestamp'] .' '. $curtime;
if(!$_SESSION || !$_SESSION['timestamp'] || $_SESSION['timestamp'] < $curtime) {
	header("Location: /index.php");
}
$user = new User($_SESSION['id']);
date_default_timezone_set('Asia/Hong_Kong');
?>
<?php $title = "Ivy League" ?>
<html>
<head>
<title><?php if(isset($title)) echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="/styles/glDatePicker.default.css" rel="stylesheet" type="text/css">
<link href="/styles/font-awesome.min.css" rel="stylesheet" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script>

		function deleteAllCookies() {
		var cookies = document.cookie.split(";");

		for (var i = 0; i < cookies.length; i++) {
			var cookie = cookies[i];
			var eqPos = cookie.indexOf("=");
			var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
			document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
			window.location = "/";
		}
}
</script>
<script type="text/javascript">
var idleTime = 0;
$(document).ready(function () {
    //Increment the idle time counter every minute.
    var idleInterval = setInterval(timerIncrement, 1000); // 1 seconds

    //Zero the idle timer on mouse movement.
    $(this).mousemove(function (e) {
        idleTime = 0;
    });
    $(this).keypress(function (e) {
        idleTime = 0;
    });
});

function timerIncrement() {
    idleTime = idleTime + 1;
    if (idleTime > 300) { // 300s = 5mins
        // alert("你已經閒置超過5分鐘，你將會被登出！");
        // deleteAllCookies();
        // window.reload();
    }
}
</script>
<style type="text/css">
	blockquote {
		white-space: pre-wrap;
	}
	h1,h2,h3,form {
		margin: 0 !important;
	}
	h1,h2,h3 {
		padding: 10px;
	}
</style>
</head>
<body style="margin: 0; padding: 0; min-height: 100%; position: relative;background-image: url('/img/bg.jpg');background-repeat: repeat-x;">
<?php $course_admin = new AccessControl('course_admin'); ?>
<?php $system_admin = new AccessControl('system_admin'); ?>
<?php $lecture_admin = new AccessControl('lecture_admin'); ?>
<?php $user_admin = new AccessControl('user_admin'); ?>

<?php $user_level = $user->get_permission()->level; ?>
<div style="margin:0px auto;max-width:1080px;background-color:white; padding-bottom: 60px;">
<a href="/index2.php"><img src="/image/banner_1.jpg" style="width:100%;"></a>
<p style="height:35px;width:100%;background-color:#ffa311;margin:0px">
<table style="color:white;white-space: nowrap; display: inline-block;" >
	<tr>
		<?php if($user_level > 1): ?>
			<th class="tg-yw4l" style="padding: 7px 20px;"><a href="/lecture.php" style="color: #fff; font-family: Heiti TC; text-decoration: none; font-weight: 300;">教學</a></th>
			<th class="tg-yw4l" style="padding: 7px 20px;"><a href="/revision.php" style="color: #fff; font-family: Heiti TC; text-decoration: none; font-weight: 300;">溫習</a></th>
		<?php endif; ?>
		<th class="tg-yw4l" style="padding: 7px 20px;"><a href="/admin.php" style="color: #fff; font-family: Heiti TC; text-decoration: none; font-weight: 300;">帳戶管理</a></th>
		<?php if($course_admin->hasReadAccess($user)): ?>
		<th class="tg-yw4l" style="padding: 7px 20px;"><a href="/admin/index.php" style="color: #fff; font-family: Heiti TC; text-decoration: none; font-weight: 300;">課程管理</a></th>
		<?php endif; ?>
		<?php if($user_admin->hasReadAccess($user)): ?>
		<th class="tg-yw4l" style="padding: 7px 20px;"><a href="/admin/user.php" style="color: #fff; font-family: Heiti TC; text-decoration: none; font-weight: 300;">用戶管理</a></th>
		<?php endif; ?>
		<?php if($system_admin->hasReadAccess($user)): ?>
		<th class="tg-yw4l" style="padding: 7px 20px;"><a href="/admin/system.php" style="color: #fff; font-family: Heiti TC; text-decoration: none; font-weight: 300;">系統管理</a></th>
		<?php endif; ?>
	</tr>
</table>
<table style="float:right;color:white;padding-right:30px" >
	<tr>
	<th>權限: <?php echo $user->get_permission()->name;?></th>
	<th class="tg-yw4l" style="padding: 7px 0px;" ><a onclick="deleteAllCookies()" style="font-family: Heiti TC;font-weight: 300;">登出</th>
	</tr>
</table>
</p> 