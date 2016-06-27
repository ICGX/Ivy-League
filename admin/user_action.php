<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
$admin = new AccessControl('user_admin');
$user = new User($_SESSION['id']);
if(!$admin->hasReadAccess($user)){
	header('Location: /prohibited.php');
}
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';

switch ($_POST['type']) {
	case 'add':
		require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';
		include $_SERVER['DOCUMENT_ROOT'].'/views/admin/user.register.php';
		require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php';
		break;
	case 'addAdmin':
		if($user->get_permission()->level == 5){
			require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';
			include $_SERVER['DOCUMENT_ROOT'].'/views/admin/user.adminregister.php';
			require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php';
		}else{
			header('Location: /admin/index.php');
		}
		break;
	case 'addParent':
		require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';
		include $_SERVER['DOCUMENT_ROOT'].'/views/admin/user.parentregister.php';
		require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php';
		break;
	case 'addAdminAction':
		if(count(User::get_user_by_username($_POST['username'])) == 0){
			$password = hash('sha256', $_POST['password']);
			switch(User::addAdmin($_POST['username'], $password, $_POST['sex'], $_POST['borndate'], $_POST['telephone'], $_POST['adress'], $_POST['email'], $_POST['level'], $_POST['subject'], $_POST['name'])){
				case 1:
					echo json_encode(array('status'=>'true'));
					break;
				default:
					echo json_encode(array('status'=>'false'));
					break;
			}
		}else{
			echo json_encode(array('status'=>'false', 'message'=>'用戶名已被使用'));
		}
		break;
	case 'addUser':
		if(count(User::get_user_by_username($_POST['username'])) == 0){
			$password = hash('sha256', $_POST['password']);
			switch(User::addUser($_POST['username'], $password, $_POST['sex'], $_POST['borndate'], $_POST['grade'], $_POST['telephone'], $_POST['adress'], $_POST['parents'], $_POST['email'], $_POST['level'], $_POST['subject'], $_POST['name'])){
				case 1:
					echo json_encode(array('status'=>'true'));
					break;
				case 5:
					echo json_encode(array('status'=>'false', 'message' => '請檢查家長用戶名稱!'));
					break;
				default:
					echo json_encode(array('status'=>'false'));
					break;
			}
		}else{
			echo json_encode(array('status'=>'false', 'message'=>'用戶名已被使用'));
		}
		break;
	case 'addParentAction':
		if(count(User::get_user_by_username($_POST['username'])) == 0){
			$password = hash('sha256', $_POST['password']);

			if(User::addParent($_POST['username'], $password, $_POST['sex'], $_POST['borndate'], $_POST['telephone'], $_POST['adress'], $_POST['email'], $_POST['name'])){
				echo json_encode(array('status'=>'true'));
			}else{
				echo json_encode(array('status'=>'false'));
			}
		}else{
			echo json_encode(array('status'=>'false', 'message'=>'用戶名已被使用'));
		}
		break;
	case 'viewUser':
		try{
			$user = User::get_user_by_username($_POST['username']);
			if($user){
				$profile = $user->get_profile();
				$parent = $user->get_parent();
				$children = $user->get_children();
				unset($profile->password);
				unset($parent->password);
				if($children){
					foreach ($children as $i => $c) {
						unset($children[$i]['password']);
					}
				}
				echo json_encode(array('status'=>'true', 'user'=>$profile, 'subject'=>$user->get_subject(), 'parent' => $parent, 'children' => $children));
			}else{
				echo json_encode(array('status'=>'false'));
			}
		} catch(Exception $e){
			echo json_encode(array('status'=>'false', 'message'=>$e->getMessage()));
		}
		break;
	case 'deleteUser':
		try{
			$user = User::get_user_by_username($_POST['username']);
			if($user){
				if($user->delete())
					echo json_encode(array('status'=>'true'));
				else
					echo json_encode(array('status'=>'false'));
			}else{
				echo json_encode(array('status'=>'false', 'message'=>'用戶不存在'));
			}
		} catch(Exception $e){
			echo json_encode(array('status'=>'false', 'message'=>$e->getMessage()));
		}
		break;
	default:
		header('Location: /admin/user.php');
		break;
}