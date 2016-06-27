<!DOCTYPE html>
<html style="background-image: url('../img/bg.jpg');background-repeat: repeat-x;">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../styles/glDatePicker.default.css" rel="stylesheet" type="text/css">
</head>
<body style="margin:0px auto;width:1080px;background-color:white;">
<a href="./index2.php"><img src="../image/banner_1.jpg" style="width:100%;"></a>
<div style="height:35px;width:100%;background-color:#ffa311;margin:-4px 0px 0px 0px">
<table style="color:white;white-space: nowrap; display: inline-block;" >
  <tr>
    <th class="tg-yw4l" style="padding: 7px 20px;"><a href="../playlist.php" style="color: #fff; font-family: Heiti TC; text-decoration: none; font-weight: 300;">教學</a></th>
    <th class="tg-yw4l" style="padding: 7px 20px;"><a href="../judge.php" style="color: #fff; font-family: Heiti TC; text-decoration: none; font-weight: 300;">溫習</a></th>
    <th class="tg-yw4l" style="padding: 7px 20px;"><a href="../admin.php?id=<?php echo $array['id']; ?>" style="color: #fff; font-family: Heiti TC; text-decoration: none; font-weight: 300;">帳戶管理</a></th>
  </tr>
</table>
<table style="float:right;color:white;padding-right:30px" >
  <tr>
  <th class="tg-yw4l" style="padding: 7px 0px;" ><a onclick="deleteAllCookies()" style="font-family: Heiti TC;font-weight: 300;">登出</th>
  </tr>
</table>
</div>

<div style="margin-top:3%;">

<form action="add.php" method="POST" id="myform">
  Whole Question:
  <input type="text" name="bq">
  <br>
  <br>
  First question: 
  <input type="text" name="1q"><br>
  <select name="1a" form="myform">
  	 	<option value="T">True</option>
  		<option value="F">False</option>
  </select>
  <br>
  <br>
  Second Question:
  <input type="text" name="2q"><br>
  <select name="2a" form="myform">
  	 	<option value="T">True</option>
  		<option value="F">False</option>
  </select>
  <br><br>
  Third Question:
  <input type="text" name="3q"><br>
  <select name="3a" form="myform">
  	 	<option value="T">True</option>
  		<option value="F">False</option>
  </select>
<br><br>
  <input type="hidden" name="type" value="question1">
  <input type="submit">
</form>

</div>
</body>
<p style="width:150%;height:60px;background-color:#006755;margin:0px;position:absolute;bottom:0px; left:-30px;"><div style="position:absolute;bottom:0px;color:#fff;font-family:Heiti TC; font-size:12px;padding-bottom:20px;">Copyright Ivy League 2016.All Rights Reserved.</div></p>   
</html>
