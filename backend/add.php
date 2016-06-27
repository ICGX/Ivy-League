<?php 
		$conn = mysqli_connect("localhost","root","123456","logining");	//连接数据库
		mysqli_select_db($conn,"logining");	//选择数据库
		mysqli_query($conn,"SET NAMES utf8");	//设定字符集
		$sql= "SELECT COUNT(*) FROM `".$_POST['type']."`";
		$result = mysqli_query($conn,$sql);	//执行SQL语句
		$array = mysqli_fetch_array($result);
		echo $array['COUNT(*)'];
		if($_POST['type']=="question2"){
			$ques = $_POST['1q'].'/'.$_POST['2q']."/".$_POST['3q'];
			$ans = $_POST['1a']."/".$_POST['2a']."/".$_POST['3a'];
			$sql = "INSERT INTO `logining`.`".$_POST['type']."` (`qtext`, `ques`, `ans`, `no`) VALUES ('".$_POST['bq']."','".$ques."','".$ans."','".$array['COUNT(*)']."')";
			$result = mysqli_query($conn,$sql);	//执行SQL语句
		}else if($_POST['type']=="question1"){
			$ques = $_POST["1q"]."/".$_POST["2q"]."/".$_POST["3q"]."/".$_POST["4q"]."/".$_POST["5q"]."/".$_POST["6q"];
			$ans = $_POST["1a"]."/".$_POST["2a"]."/".$_POST["3a"]."/".$_POST["4a"]."/".$_POST["5a"]."/".$_POST["6a"];
			$word = $_POST["2q"]."/".$_POST["1q"]."/".$_POST["6q"]."/".$_POST["5q"]."/".$_POST["3q"]."/".$_POST["4q"];
			$sql = "INSERT INTO `logining`.`".$_POST['type']."` (`qtext`, `content`,`word`,`ans`, `no`) VALUES ('".$_POST['bq']."','".$ques."','".$word."','".$ans."','".$array['COUNT(*)']."')";
			$result = mysqli_query($conn,$sql);	//执行SQL语句
		}
		echo $result;
		echo $sql;

?>