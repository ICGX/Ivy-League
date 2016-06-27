<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
if(!isset($_SESSION['id'])){
	header('Location: /index.php');
}
$user = new User($_SESSION['id']);
if(!AccessControl::checkIP($_SERVER['REMOTE_ADDR'])){
	header('Location: /prohibited.php');
}
include 'views/header.php'; 

$user = new User($_SESSION['id']);
$profile = $user->get_profile();
$permission = $user->get_permission();
$grade = array('', '一年級', '二年級','三年級','四年級','五年級','六年級');
?>
<style type="text/css">
	.tg  {border-collapse:collapse;border-spacing:0;}
	.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal; border-color: #fff;}
	.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color: #fff;}
	.tg .tg-yw4l{vertical-align:top}
</style>
<div  style="padding:3% 0px;">
	<table class="tg">
		<tr>
			<th class="tg-yw4l" style="background-color: #2cb676; font-family: Heiti TC;text-align: left;">用戶id</th>
			<th class="tg-yw4l" style="background-color: #a8e2c7; font-family: Heiti TC; text-align: left;"><?php echo $profile->id;?></th>
		</tr>
		<tr>
			<th class="tg-yw4l" style="background-color: #2cb676; font-family: Heiti TC;text-align: left;">權限</th>
			<th class="tg-yw4l" style="background-color: #a8e2c7; font-family: Heiti TC; text-align: left;"><?php echo $permission->name;?></th>
		</tr>
		<tr>
			<th class="tg-yw4l" style="background-color: #2cb676; font-family: Heiti TC; text-align: left;">用戶名</th>
			<th class="tg-yw4l" style="background-color: #a8e2c7; font-family: Heiti TC; text-align: left;"><?php echo $profile->username;?></th>
		</tr>
		<tr>
			<td class="tg-yw4l" style="background-color: #2cb676; font-family: Heiti TC;">性別</td>
			<?php $sex = $profile->sex == 'nam'?'男':'女';?>
			<td class="tg-yw4l" style="background-color: #a8e2c7; font-family: Heiti TC;"><?php echo $sex; ?></td>
		</tr>
		<tr>
			<td class="tg-yw4l" style="background-color: #2cb676; font-family: Heiti TC;">出生日期</td>
			<td class="tg-yw4l" style="background-color: #a8e2c7; font-family: Heiti TC;"><?php echo $profile->borndate;?></td>
		</tr>
		<tr>
			<td class="tg-yw4l" style="background-color: #2cb676; font-family: Heiti TC;">年級</td>
			<td class="tg-yw4l" style="background-color: #a8e2c7; font-family: Heiti TC;"><?php echo $grade[$profile->grade];?></td>
		</tr>
		<tr>
			<td class="tg-yw4l" style="background-color: #2cb676; font-family: Heiti TC;">科目</td>
			<?php
			$subjects = array();
			foreach($user->get_subject() as $s){
				array_push($subjects, $s['name']);
			}
			?>
			<td class="tg-yw4l" style="background-color: #a8e2c7; font-family: Heiti TC;"><?php echo implode('/', $subjects);?></td>
		</tr>
		<tr>
			<td class="tg-yw4l" style="background-color: #2cb676; font-family: Heiti TC;">電話號碼</td>
			<td class="tg-yw4l" style="background-color: #a8e2c7; font-family: Heiti TC;"><?php echo $profile->telephone?></td>
		</tr>
		<tr>
			<td class="tg-yw4l" style="background-color: #2cb676; font-family: Heiti TC;">地址</td>
			<td class="tg-yw4l" style="background-color: #a8e2c7; font-family: Heiti TC;"><?php echo $profile->address?></td>
		</tr>
		<tr>
			<td class="tg-yw4l" style="background-color: #2cb676; font-family: Heiti TC;">家長姓名</td>
			<td class="tg-yw4l" style="background-color: #a8e2c7; font-family: Heiti TC;"><?php echo $profile->parents?></td>
		</tr>
		<tr>
			<td class="tg-yw4l" style="background-color: #2cb676; font-family: Heiti TC;">電郵地址</td>
			<td class="tg-yw4l" style="background-color: #a8e2c7; font-family: Heiti TC;"><?php echo $profile->email?></td>
		</tr>
	</table>
</div>

<?php include 'views/footer.php'; ?>
