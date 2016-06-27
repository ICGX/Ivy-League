<?php 
		echo $_POST['field'];

		$conn = mysqli_connect("localhost","ivyleague","5:1etMiyTrfdmxNNbqyfwisiyxuNeSaaxZaXjhxLfrSD@747","logining");	//连接数据库
		mysqli_select_db($conn,"logining");	//选择数据库
		mysqli_query($conn,"SET NAMES utf8");	//设定字符集
		$sql = "UPDATE `logining`.`user` SET `".$_POST['field']."` = '".$_POST['value']."' WHERE `user`.`id`=".$_POST['id'];	//SQL语句
		$result = mysqli_query($conn,$sql);	//执行SQL语句
		$array = mysqli_fetch_array($result);
		var_dump($array);
	?>