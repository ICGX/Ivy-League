<?php

$q = new $q->type($q->qid);
// var_dump($q);
?>

<div style="width:60%; display:inline-block; vertical-align:top;">
	<h3><?php echo $q->get_name(); ?></h3>
	<blockquote><?php echo $q->getQuestion(); ?></blockquote>
	<?php foreach($q->getImage() as $image): ?>
		<?php if($image['type'] != 'description_image') continue; ?>
		<img style="max-width:1080px" src="/uploads/<?php echo $image['id'];?>.<?php echo $image['ext']; ?>" alt="description photo">
	<?php endforeach; ?>
	
	<?php $a = $q->getAnswers(); ?>
	<?php $ma = $q->getModelAnswer(); ?>
	<?php $punct = preg_split('//u', $a->answer, -1, PREG_SPLIT_NO_EMPTY); ?>
	<?php
		$punct_count = 0;
		$selector = '';
		foreach($punct as $p){
			$selector .= '<option value="'.$p.'">'.$p.'</option>';
		}
	?>
	<?php for($i = 0; $i < mb_strlen($ma->answer, 'utf-8'); $i++): ?>
		<?php $chr = mb_substr($ma->answer, $i, 1, 'utf-8'); ?>
		<?php if(array_search($chr, $punct) !== false): ?>
			&nbsp;
			<select id="punct-<?php echo $punct_count++;?>">
				<?php echo $selector; ?>
			</select>
			&nbsp;
		<?php else: ?>
			<?php echo $chr; ?>
		<?php endif; ?>
	<?php endfor; ?>
</div>
<div id="indication" style="width:30%; display:inline-block; vertical-align: top;"></div>
<div id="err" style="width:30%;  display:inline-block; vertical-align: top;"></div>
<script type="text/javascript">
$(document).ready(function(){
	$('.submit').on('click', function(e){
		e.preventDefault();
		<?php $count = 0; ?>
		var punct_count = <?php echo $punct_count; ?>;
		var answer = '<?php for($i = 0; $i < mb_strlen($ma->answer, 'utf-8'); $i++){
			$chr = mb_substr($ma->answer, $i, 1, 'utf-8');
			if(array_search($chr, $punct) !== false){
				echo "'+$('#punct-".($count++)."').val()+'";
			}else{
				echo $chr;
			}
		}?>';
		var qid = <?php echo $q->get_id(); ?>;
		var qtype = '<?php echo $q->get_type(); ?>';
		$.ajax({
			url: '/ajax.php',
			type: 'POST',
			data: {
				type: 'checkAnswer',
				uid: <?php echo $user->get_profile()->id; ?>,
				qsid: <?php echo $qs->get_id();?>,
				qid: qid,
				qtype: qtype,
				answer: answer
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