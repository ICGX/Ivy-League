<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
$admin = new AccessControl('system_admin');
$user = new User($_SESSION['id']);
if(!$admin->hasReadAccess($user)){
	header('Location: /prohibited.php');
}

switch ($_POST['type']) {
	case 'add':
		AccessControl::addIP($_POST['ip']);
		break;
	case 'delete':
		AccessControl::deleteIP($_POST['id']);
		break;
}
header('Location: /admin/system.php');