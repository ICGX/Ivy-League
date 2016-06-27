<?php require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php'; ?>
<?php
session_start();
error_reporting(0);//将Notice隐藏
if(isset($_POST["submit"]) && $_POST["submit"] == "登錄")
{
	$user = $_POST["username"];
	$psw = $_POST["password"];
	if($user == "" || $psw == "")
	{
		echo "<script>alert('請輸入用户名或密碼！'); history.go(-1);</script>";
	}
	else
	{
		$user = User::authenticate($_POST['username'], $_POST['password']);
		if($user){
			// echo "登录成功，欢迎".$user['username']."回来";
			$_SESSION['id'] = $user['id'];
			$_SESSION['timestamp'] = time() + (3600*24);
			// setcookie('ivyleague_id', $user['id'], 3600*24);
			header("Location: ./index2.php");
		}else{
			echo "<script>alert('用户名或密碼不正確！');history.go(-1);</script>";
		}
	}
}
else
{
	echo "<script>alert('提交未成功！'); history.go(-1);</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>登錄成功界面</title>
</head>
<body>

    <?php //把用户的权限存放于cookies
	 //    $conn = mysqli_connect("localhost","ivyleague","","logining");	//连接数据库
		// mysqli_select_db($conn,"logining");	//选择数据库
		// mysqli_query($conn,"SET NAMES utf8");	//设定字符集
		// $sql = "select competence from user where username = '$_POST[username]'";	//SQL语句
		// $result = mysqli_query($conn,$sql);	//执行SQL语句
		// $competence = mysqli_fetch_array($result);
		// setcookie('competence',$competence[competence],time()+3600*24);//设置cookie并且时间为24小时
		// mysql_close($conn);
    ?>
    <?php //把用户的id存放于session
	 //    $conn = mysqli_connect("localhost","ivyleague","","logining");	//连接数据库
		// mysqli_select_db($conn,"logining");	//选择数据库
		// mysqli_query($conn,"SET NAMES utf8");	//设定字符集
		// $sql = "select id from user where username = '$_POST[username]'";	//SQL语句
		// $result = mysqli_query($conn,$sql);	//执行SQL语句
		// $id = mysqli_fetch_array($result);
		// session_start();
		// //$_SESSION['id'] = hash('sha256',$id[id]);
		// $_SESSION['id'] = $id[id];
		// //echo "id=". $_SESSION['id'];
		// mysql_close($conn);
    ?>
    <br/>
    <?php
    	//print_r($_COOKIE);
    	//print_r($_COOKIE[competence]);
    	echo "competence=". $_COOKIE[competence];
    ?>
    <br/>
    <?php
		echo "id=". $_SESSION['id'];
		

    ?>
</body>
</html>

