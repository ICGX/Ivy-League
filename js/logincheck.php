
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>登录成功界面</title>
</head>
<body>
	<?php
    error_reporting(0);//将Notice隐藏
	if(isset($_POST["submit"]) && $_POST["submit"] == "登錄")
	{
		$user = $_POST["username"];
		$psw = $_POST["password"];
		if($user == "" || $psw == "")
		{
			echo "<script>alert('请输入用户名或密码！'); history.go(-1);</script>";
		}
		else
		{
			$conn = mysqli_connect("localhost","root","123456","logining");
			mysqli_select_db($conn,"logining");
			mysqli_query($conn,"SET NAMES utf8");
			$sha = hash('sha256',$_POST[password]);
			$sql = "select username,password from user where username = '$_POST[username]' and password = '$sha'";
			$result = mysqli_query($conn,$sql);
			$num = mysqli_num_rows($result);
			if($num)
			{
				$row = mysqli_fetch_array($result);	//将数据以索引方式储存在数组中
				echo "登录成功，欢迎".$row[0]."回来";
			}
			else
			{	
				 echo "<script>alert('用户名或密码不正确！');history.go(-1);</script>";
			}
			mysql_close($conn);
		}
	}
	else
	{
		echo "<script>alert('提交未成功！'); history.go(-1);</script>";
	}
	?>
    <?php //把用户的权限存放于cookies
	    $conn = mysqli_connect("localhost","root","","logining");	//连接数据库
		mysqli_select_db($conn,"logining");	//选择数据库
		mysqli_query($conn,"SET NAMES utf8");	//设定字符集
		$sql = "select competence from user where username = '$_POST[username]'";	//SQL语句
		$result = mysqli_query($conn,$sql);	//执行SQL语句
		$competence = mysqli_fetch_array($result);
		setcookie('competence',$competence[competence],time()+3600*24);//设置cookie并且时间为24小时
		mysql_close($conn);
    ?>
    <?php //把用户的id存放于session
	    $conn = mysqli_connect("localhost","root","","lphpjogining");	//连接数据库
		mysqli_select_db($conn,"logining");	//选择数据库
		mysqli_query($conn,"SET NAMES utf8");	//设定字符集
		$sql = "select id from user where username = '$_POST[username]'";	//SQL语句
		$result = mysqli_query($conn,$sql);	//执行SQL语句
		$id = mysqli_fetch_array($result);
		session_start();
		$_SESSION['id'] = hash('sha256',$id[id]);
		//echo "id=". $_SESSION['id'];
		mysql_close($conn);
    ?>
    <?php
    	print_r($_COOKIE);
		echo "id=". $_SESSION['id'];
    ?>
</body>
</html>

