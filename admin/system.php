<?php 
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
if(!isset($_SESSION['id'])){
	header('Location: /index.php');
}
$admin = new AccessControl('system_admin');
$user = new User($_SESSION['id']);
if(!$admin->hasReadAccess($user)){
	header('Location: /prohibited.php');
}
require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';
?>
<div>
	<?php $ip_list = AccessControl::getIPs(); ?>
	<h2>系統管理</h2>
	<h3>IP 存取白名單</h3>
	<form action="system_action.php" method="POST">
		<input type="hidden" name="type" value="delete">
		<ul>
		<?php foreach($ip_list as $ip): ?>
			<li><?php echo $ip['ip']; ?> <button type="submit" name="id" value="<?php echo $ip['id']; ?>">刪除</button></li>
		<?php endforeach; ?>
		</ul>
	</form>
	<h3>新增 IP 至存取白名單</h3>
	<form action="system_action.php" method="POST">
		<input type="hidden" name="type" value="add">
		<input type="text" name="ip" placeholder="IP 地址">
		<input type="submit" value="增加">
	</form>
</div>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php'; ?>