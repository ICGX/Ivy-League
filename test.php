<?php include 'views/header.php'; ?>

<?php
		error_reporting(0);

		$a = 0;
		if($_GET['id']!=NULL){
			$a = $_GET['id'];
		}
		//$array['content'] is the question
		$conn = mysqli_connect("localhost","ivyleague","5:1etMiyTrfdmxNNbqyfwisiyxuNeSaaxZaXjhxLfrSD@747","logining");	//连接数据库
		mysqli_select_db($conn,"logining");	//选择数据库
		mysqli_query($conn,"SET NAMES utf8");	//设定字符集
		$sql= "SELECT * FROM `question1` WHERE `question1`.`no`=".$a;
		$result = mysqli_query($conn,$sql);	//执行SQL语句
		$array = mysqli_fetch_array($result);
		//var_dump($array);
		$question = explode('/', $array['content']);
		//var_dump($question);

?>

<div style="font-family:Heiti TC; margin-top:30px; width:60%; display: inline-block;">
	<p id="qtext"></p>
	<p id="word"  style="background-color:#a8e2c7; width:240px; margin-top:10px; margin-bottom:10px; padding: 5px;"></p>
	<p id="content" style="line-height: 30px;"></p>	
	<script>
		function checkans(){
		//alert("checked");
		var no = 0
		for(var i=0;i<6;i++){
			var sd = i+1;
			var usrans = document.getElementById("s"+sd).value;
			//alert(ans[i]);
			if(usrans==ans[i]){
				no++;
			}
			
		}
		if(no==6){
			var audio = new Audio('./effect/happysound/01.mp3');
			audio.play();
			document.getElementById("right").style.display = "block";
		}else{
			
		}
	}
	var ques = "<?php echo $array['content']?>";
	var ans  = "<?php echo $array['ans']?>";
	var word = "<?php echo $array['word']?>";
	var qt = "<?php echo $array['qtext'];?>"
	ques = ques.split("/");
	ans  = ans.split("/");
	word = word.split("/");
	qt = qt.split("/");

	var words="";
	var contents="";
	for(var i=0;i<6;i++){
		words += word[i]+" ";
		contents += (i+1)+":"+ques[i]+"<br>";

	} 
	document.getElementById('word').innerHTML = words;
	document.getElementById('content').innerHTML = contents;
	for(var a=0;a<6;a++){
		document.write((a+1)+".<input type='text' id='s"+(a+1)+"' style='margin-bottom:10px'/><br>");
	}
	document.getElementById("qtext").innerHTML = qt;
</script>
<br>
<button onclick="checkans()" style="padding: 0; border: none;"><img src="image/btn_submit.png" style="width: 130px;"></img></button>
<p style="display: inline;"><a href="judge.php"><img src="image/btn_previous.png" style="width: 130px;"></img></a></p>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<div style="width:20%; display: inline-block;">
	<img src="./effect/1.jpg" style="display:none; width:200px" id="right"> 
	<img src="./effect/b01.png" style="display:none; width:200px;" id="wrong"> 
</div>

<?php include 'views/footer.php'; ?>