<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
if(!isset($_SESSION['id'])){
	header('Location: /index.php');
}
$user = new User($_SESSION['id']);
if(!AccessControl::checkIP($_SERVER['REMOTE_ADDR'])){
	header('Location: /prohibited.php');
}
require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';
?>
<div style="margin-top:3%;">
	<?php if($user_level > 1): ?>
		<a href="./lecture.php"><img src="image/btn_learn.png" style="height:200px"></a>
		<a href="./revision.php"><img src="image/btn_revision.png" style="height:200px"></a>
	<?php endif; ?>
	<a href="./admin.php?id=<?php echo $_SESSION['id']; ?>"><img src="image/btn_account.png" style="height:200px"></a>
	<div id="mydate"
	style="width:15%; height:7%;float:right;margin-right:50px;margin-top:0px;float:top;"></div>
	<div  gldp-id="mydate" />
		<div id="test"></div>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="glDatePicker.min.js"></script>

		<script type="text/javascript">
			$(window).load(function()
			{
				$("#mydate").glDatePicker({
					showAlways: true,
					dowNames: ['日','一', '二', '三','四','五','六'],
					monthNames: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
					onClick: (function(el, cell, date, data) {
						$.ajax({
							url: "/ajax.php",
							type: "POST",
							data: {
								type: "schedule",
								uid: <?php echo $_SESSION['id']; ?>,
								timestamp: date.valueOf()/1000
							}
						}).done(function(data){
							alert(data);
						});
					})

				});
			});
		</script>
	</div>
	<div>
		<?php 
			$level = $user->get_permission();
			if($level->level == 2):
				$today = strtotime(date('Y-m-d', time()));
				$question_set = $user->get_calendar_questionset($today);
		?>
			<h2>適時練習</h2>
			<?php if($question_set): ?>
				<?php foreach($question_set as $qs): ?>
					<p><a href="/revision/before.php?qsid=<?php echo $qs['qsid']; ?>"><?php echo $qs['name'];?> - <?php echo $qs['qsname']; ?></a></p>
				<?php endforeach; ?>
			<?php else: ?>
				<p>今天沒有練習!</p>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
<?php
include 'views/footer.php';
?>