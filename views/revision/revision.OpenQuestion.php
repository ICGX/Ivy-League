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

	<textarea id="answer"></textarea>
</div>
<div id="indication" style="width:30%; display:inline-block; vertical-align: top;"></div>
<script type="text/javascript">
	$(document).ready(function(){
		$('.submit').on('click', function(e){
			e.preventDefault();
			var qid = <?php echo $q->get_id(); ?>;
			var qtype = '<?php echo $q->get_type(); ?>';
			// console.log($('#answer').val());
			$.ajax({
				url: '/ajax.php',
				type: 'POST',
				data: {
					type: 'checkAnswer',
					qid: qid,
					qtype: qtype,
					qsid: <?php echo $qs->get_id();?>,
					uid: <?php echo $user->get_profile()->id; ?>,
					answer: $('#answer').val()
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
				}
			});
			return false;
		})
	})
</script>