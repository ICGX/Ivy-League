<?php
		
		//$array['content'] is the question
		error_reporting(0);

		$a = 0;
		if($_GET['id']!=NULL){
			$a = $_GET['id'];
		}
		$conn = mysqli_connect("localhost","ivyleague","5:1etMiyTrfdmxNNbqyfwisiyxuNeSaaxZaXjhxLfrSD@747","logining");	//连接数据库
		mysqli_select_db($conn,"logining");	//选择数据库
		mysqli_query($conn,"SET NAMES utf8");	//设定字符集
		$sql= "SELECT * FROM `question2` WHERE `question2`.`no`=".$a;
		$result = mysqli_query($conn,$sql);	//执行SQL语句
		$array = mysqli_fetch_array($result);
		//var_dump($array);
		//$question = explode('/', $array['content']);
		//var_dump($question);

?>
<?php include 'views/header.php' ?>
<div style="font-family:Heiti TC; margin-top:30px; width:60%; display: inline-block;">
	<script type="text/javascript" src="http://l2.io/ip.js?var=myip"></script>
	<!-- ^^^^ -->
	<script>alert(myip);</script>
</div>
<?php include 'views/footer.php' ?>