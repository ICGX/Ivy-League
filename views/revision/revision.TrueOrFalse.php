<?php

$q = new $q->type($q->qid);
?>

<div style="width:60%; display:inline-block; vertical-align:top;">
	<h3><?php echo $q->get_name(); ?></h3>
	<blockquote><?php echo $q->getQuestion(); ?></blockquote>
	<?php foreach($q->getImage() as $image): ?>
		<?php if($image['type'] != 'description_image') continue; ?>
		<img style="max-width:1080px" src="/uploads/<?php echo $image['id'];?>.<?php echo $image['ext']; ?>" alt="description photo">
	<?php endforeach; ?>
		<label><input class="answer" type="radio" name="answer" value="true" <?php if($history && $history->answer == 'true') echo 'checked';?>> 正確</label>
		<label><input class="answer" type="radio" name="answer" value="false" <?php if($history && $history->answer == 'false') echo 'checked';?>> 錯誤</label>
</div>
<div id="indication" style="width:30%; display:inline-block; vertical-align: top;"></div>
<div id="err" style="width:30%;  display:inline-block; vertical-align: top;"></div>
<script type="text/javascript">
	$(document).ready(function(){
		$('.submit').on('click', function(e){
			e.preventDefault();
			if($('.answer:checked').length <= 0){
				alert("請回答題目!");
				return false;
			}
			var qid = <?php echo $q->get_id(); ?>;
			var qtype = '<?php echo $q->get_type(); ?>';
			$.ajax({
				url: '/ajax.php',
				type: 'POST',
				data: {
					type: 'checkAnswer',
					qid: qid,
					qtype: qtype,
					uid: <?php echo $user->get_profile()->id; ?>,
					qsid: <?php echo $qs->get_id();?>,
					answer: $('.answer:checked').val()
				}
			}).done(function(data){
				// console.log(data);
				var result = JSON.parse(data);
				// console.log(result);
				if(result.status == "true"){
					// console.log("TRUE");
					var audio = new Audio('/effect/happysound/01.mp3');
					audio.play();
					$('#indication').html('<img src="/effect/1.jpg" alt="Good">');
					// window.location.href = $(e.currentTarget).attr('href');
				}else{
					$('#err').html('');
					for(var i in result.image){
						$('#err').append('<img style="width:100%;" src="/uploads/'+result.image[i]+'" alt="error">');
					}
					var audio = new Audio('/effect/sadsound/1.mp3');
					audio.play();
					$('#indication').html('<img src="/effect/b01.png" alt="Bad">');
				}
			});
			return false;
		})
	})
</script>