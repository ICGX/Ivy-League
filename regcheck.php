<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>註冊成功界面</title>
</head>
<body>
<?php
    error_reporting(0);//将Notice隐藏
	if(isset($_POST["Submit"]) && $_POST["Submit"] == "註冊")
	{
		$user = $_POST["username"];
		$psw = $_POST["password"];
		$psw_confirm = $_POST["confirm"];
		$sex = $_POST["sex"];
		$borndate = $_POST["borndate"];
		$model = $_POST["model"];
		$grade = $_POST["grade"];
		$subject = $_POST["subject"];
		$telephone = $_POST["telephone"];
		$adress = $_POST["adress"];
		$registertime = date('Y-m-d H:i:s');
		$email = $_POST["email"];
		$parents = $_POST["parents"];
		$array = $_POST["subject"];
		$subject = implode("/",$array);
		if($user == "" || $psw == "" || $psw_confirm == "" || $email == "")
		{
			echo "<script>alert('请确认信息完整性！'); history.go(-1);</script>";
		}
		else
		{
			if($psw == $psw_confirm)
			{
				$conn = mysqli_connect("localhost","root","123456","logining");	//连接数据库
				mysqli_select_db($conn,"logining");	//选择数据库
			    mysqli_query($conn,"SET NAMES utf8");	//设定字符集
				$sql = "select username from user where username = '$_POST[username]'";	//SQL语句
				$result = mysqli_query($conn,$sql);	//执行SQL语句
				$num = mysqli_num_rows($result);	//统计执行结果影响的行数
				if($num)	//如果已经存在该用户
				{
					echo "<script>alert('用户名已存在'); history.go(-1);</script>";
				}
				else	//不存在当前注册用户名称
				{   
					//$sha = sha1($_POST[password]);
					$sha = hash('sha256',$_POST[password]);
					$sql_insert = "insert into user (username,password,email,registertime,sex,borndate,model,grade,subject,telephone,adress,parents) values('$_POST[username]','$sha','$_POST[email]','$registertime','$sex','$borndate','$model','$grade','$subject','$telephone','$adress','$parents')";
					//$sql_insert = "insert into user (username,password) values('$_POST[username]','$_POST[password]')";
					$res_insert = mysqli_query($conn,$sql_insert);
					//$num_insert = mysql_num_rows($res_insert);
					if($res_insert)
					{
						echo "<script>alert('注册成功'); history.go(-1);</script>";
					}
					else
					{
						echo "<script>alert('系统繁忙，请稍候！'); history.go(-1);</script>";
					}
				}
				mysql_close($conn);
			}
			else
			{
				echo "<script>alert('密码不一致！'); history.go(-1);</script>";
			}
		}
	}
	else
	{
		echo "<script>alert('提交未成功！'); history.go(-1);</script>";
	}
?>	
</body>
</head>
</html>
