<?php

$q = new $q->type($q->qid);
if($history){
	$history_map = array();
	$attempt_history = explode('|::|', $history->answer);
	foreach($attempt_history as $a){
		$split = explode('|:|', $a);
		$history_map[$split[1]] = $split[0];
	}
}else{
	$attempt_history = null;
}
// var_dump($history_map);
// var_dump($q);
?>

<div style="width:60%; display:inline-block; vertical-align:top;">
	<h3><?php echo $q->get_name(); ?></h3>
	<blockquote><?php echo $q->getQuestion(); ?></blockquote>
	<?php foreach($q->getImage() as $image): ?>
		<?php if($image['type'] != 'description_image') continue; ?>
		<img style="max-width:1080px" src="/uploads/<?php echo $image['id'];?>.<?php echo $image['ext']; ?>" alt="description photo">
	<?php endforeach; ?>
	<?php $answers = $q->getAnswers(); ?>
	<?php $ansCount = count($answers); ?>
	<table>
	<?php foreach($answers as $i => $a): ?>
		
		<tr>
			<td><?php echo $a; ?></td>
			<td>
				<select id="answer_<?php echo $i; ?>">
				<?php for($j = 1; $j <= $ansCount; $j++): ?>
					<?php
					$selected = false;
					if($history && $history_map[$a] == $j-1) $selected = true;
					?>
					<option value="<?php echo $j-1; ?>|:|<?php echo $a;?>" <?php if($selected) echo 'selected'; ?>><?php echo $j; ?></option>
				<?php endfor; ?>
				</select>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
</div>
<div id="indication" style="width:30%; display:inline-block; vertical-align: top;"></div>
<div id="err" style="width:30%;  display:inline-block; vertical-align: top;"></div>
<script type="text/javascript">
	var length = <?php echo $ansCount; ?>;
	$(document).ready(function(){
		$('.submit').on('click', function(e){
			e.preventDefault();
			var answer = [];
			for(var i = 0; i < length; i++){
				answer.push($('#answer_'+i+' option:selected').val());
			}
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