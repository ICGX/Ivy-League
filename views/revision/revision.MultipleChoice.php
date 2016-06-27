<?php

$q = new $q->type($q->qid);
// var_dump($q);
?>

<div style="width:60%; display:inline-block; vertical-align:top;">
	<h3><?php echo $q->get_name(); ?></h3>
	<blockquote><?php echo $q->getQuestion(); ?></blockquote>
	<?php
	$modelAnswer = $q->getModelAnswer();
	$answers = array_merge($modelAnswer, $q->getAnswers()); 
	shuffle($answers);
	?>
	<?php foreach($q->getImage() as $image): ?>
		<?php if($image['type'] != 'description_image') continue; ?>
		<img style="max-width:1080px; display: block;" src="/uploads/<?php echo $image['id'];?>.<?php echo $image['ext']; ?>" alt="description photo">
	<?php endforeach; ?>
	<?php if(count($modelAnswer) > 1): ?>
		<?php foreach($answers as $a): ?>
			<p>
				<input class="answer" type="check" name="answer" value="<?php echo $a['answer']; ?>">
				<span><?php echo $a['answer']; ?></span>
			</p>
		<?php endforeach; ?>
	<?php else: ?>
		<?php foreach($answers as $a): ?>
			<p>
				<input class="answer" type="radio" name="answer" value="<?php echo $a['answer']; ?>" <?php if($history->answer && $history->answer == $a['answer']) echo 'checked'; ?>>
				<span><?php echo $a['answer']; ?></span>
			</p>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
<div id="indication" style="width:30%; display:inline-block; vertical-align: top;"></div>
<div id="err" style="width:30%;  display:inline-block; vertical-align: top;"></div>
<script type="text/javascript">
<?php if(count($modelAnswer) > 1): ?>
	
<?php else: ?>
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
					uid: <?php echo $user->get_profile()->id; ?>,
					qtype: qtype,
					answer: [$('.answer:checked').val()]
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
		});
	})
<?php endif; ?>
</script>