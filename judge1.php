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
		// var_dump($array);
		//$question = explode('/', $array['content']);
		//var_dump($question);

?>
<?php include 'views/header.php' ?>

<div style="font-family:Heiti TC; margin-top:30px; width:60%; display: inline-block;">
	<p id="qtext" ></p>
	<p id="word" ></p>
	<p id="content"></p>

	<script>
		function checkans(){
		//alert("checked");
		var no = 0
		for(var i=0;i<3;i++){
			var sd = i+1;
			var usrans = document.getElementById("d"+sd).value;
			//alert(ans[i]);
			if(usrans==ans[i]){
				no++;
			}
			
		}
		if(no==3){
			var audio = new Audio('./effect/happysound/01.mp3');
			audio.play();
			document.getElementById("right").style.display = "block";
		}else{
			var audio = new Audio('./effect/sadsound/1.mp3');
			audio.play();
			document.getElementById("wrong").style.display = "block";	
		}
	}
	
	
	var ans  = "<?php echo $array['ans']?>";
	var ques = "<?php echo $array['ques']?>";
	var qt = "<?php echo $array['qtext'];?>";
	ques = ques.split("/");
	ans  = ans.split("/");
	//word = word.split("/");
	//qt = qt.split("/");

	document.getElementById('qtext').innerHTML=qt;
	for(var a=0;a<3;a++){
		document.write("<p style='margin:10px;margin-left:0px'>"+(a+1)+": "+ques[a]+"</p>");
		document.write('<select id="d'+(a+1)+'"  style="margin-left:30px;">');
		document.write("<option value='T'>True</option>");
		document.write("<option value='F'>False</option>");
		document.write('</select><br>');
	}
	
</script>
<br>
<br>
<br>
<button onclick="checkans()" style="padding: 0; border: none; "><img src="image/btn_submit.png" style="width: 130px;"></img></button>
<p style="display: inline;"><a href="test.php"><img src="image/btn_next.png" style="width: 130px; margin-left: 10px;"></img></a></p>
</div>

<div style="width:20%; display: inline-block;">
	<img src="./effect/1.jpg" style="display:none; width:200px;" id="right">
	<img src="./effect/b01.png" style="display:none; width:200px;" id="wrong"> 
</div>

<?php include 'views/footer.php' ?>