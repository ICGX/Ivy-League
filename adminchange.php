<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
		$conn = mysqli_connect("localhost","root","123456","logining");	//连接数据库
		mysqli_select_db($conn,"logining");	//选择数据库
		mysqli_query($conn,"SET NAMES utf8");	//设定字符集
		$sql = "select * from `user` where `user`.`id`=".$_GET['id'];	//SQL语句
		$result = mysqli_query($conn,$sql);	//执行SQL语句
		$array = mysqli_fetch_array($result);
		//echo $sql;
		//echo $array['username'];
		
		//var_dump($array);

?>
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg .tg-yw4l{vertical-align:top}
</style>
<form action="change.php" id="carform" method="POST">
  user-ID:<input type="text" name="id">
  value:<input type="text" name="value">
  <input type="submit">
</form>
<br>
<select name="field" form="carform">
  
  <option value="username">用戶名</option>
  <option value="password">密碼</option>
  <option value="sex">性別</option>
  <option value="grade">級別</option>
  <option value="subject">科目</option>
  <option value="telephone">電話號碼</option>
  <option value="address">地址</option>
  <option value="parents">家長姓名</option>
  <option value="email">電郵地址</option>




</select>
 

</body>
</html>