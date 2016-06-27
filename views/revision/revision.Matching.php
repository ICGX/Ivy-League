<?php

$q = new $q->type($q->qid);
// var_dump($q);
if($history){
	$history_map = array();
	$attempt_history = explode('|::|', $history->answer);
}else{
	$attempt_history = null;
}
?>

<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Heiti TC;font-size:14px;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;}
.tg th{font-family:Heiti TC, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;}
.tg .tg-yw4l{vertical-align:top}
</style>



<div style="width:60%; display:inline-block; vertical-align:top;">
	<h3><?php echo $q->get_name(); ?></h3>
	<blockquote><?php echo $q->getQuestion(); ?></blockquote>
	<?php
	$modelAnswer = $q->getModelAnswer();
	$answers = array_merge($modelAnswer, $q->getAnswers()); 
	shuffle($answers);
	?>
	<?php foreach($q->getImage() as $image): ?>
		<div>
			<?php if($image['type'] != 'description_image') continue; ?>
			<img style="max-width:1080px; max-height:400px; display: block;" src="/uploads/<?php echo $image['id'];?>.<?php echo $image['ext']; ?>" alt="description photo">
		</div>
	<?php endforeach; ?>
	<?php $answers = $q->getShuffledAnswers(); ?>
	<div style="display: flex">
		<div id="left" style="width: 15%; display: inline-block;"></div>
		<div style="width: 310px; display: inline-block; text-align: center;">
			<canvas id="linking" width="200"></canvas>
		</div>
		<div id="right" style="width: 15%; display: inline-block;"></div>
	</div>
</div>
<div id="indication" style="width:30%; display:inline-block; vertical-align: top;"></div>
<div id="err" style="width:30%;  display:inline-block; vertical-align: top;"></div>
<script type="text/javascript">
	<?php if($attempt_history): ?>
		var history_map = ['<?php echo implode("','", $attempt_history);?>'];
	<?php else: ?>
		var history_map = null;
	<?php endif; ?>
	var left = ['<?php echo implode('\',\'',$answers[0]); ?>'];
	var right = ['<?php echo implode('\',\'',$answers[1]); ?>'];
	var c = document.getElementById('linking');
	var ctx = c.getContext("2d");
	var inc =0;
	var x=0;
	var y=0;

	var ref = {left:{}, right:{}};
	var clickingDot = [];
	var links = [];
	var cacheDot = null;

	function link(a, b){
		for(var i in links){
			if((links[i][0] == a.text && links[i][1] == b.text) 
				|| (links[i][0] == b.text && links[i][1] == a.text)){
				alert("你已經連過這條線了");
				return;
			}
		}
		ctx.beginPath();
		ctx.moveTo(a.x, a.y);
		ctx.lineTo(b.x, b.y);
		ctx.stroke();
		links.push([a.text,b.text]);
	}

	function clickDot(dot){
		if(cacheDot){
			// Second click
			if(cacheDot == dot){
				alert("請勿選擇重複答案");
			}else if(cacheDot.position == dot.position){
				alert("請勿選擇相同欄目的答案");
			}else{
				link(cacheDot, dot);
				console.log("Pair: ", cacheDot, dot);
			}
			cacheDot = null;
		}else{
			// First click
			cacheDot = dot;
			console.log("Saved:", cacheDot);
		}
	}

	function relMouseCoords(event){
	    var totalOffsetX = 0;
	    var totalOffsetY = 0;
	    var canvasX = 0;
	    var canvasY = 0;
	    var currentElement = this;

	    do{
	        totalOffsetX += currentElement.offsetLeft - currentElement.scrollLeft;
	        totalOffsetY += currentElement.offsetTop - currentElement.scrollTop;
	    }
	    while(currentElement = currentElement.offsetParent)

	    canvasX = event.pageX - totalOffsetX;
	    canvasY = event.pageY - totalOffsetY;

	    return {x:canvasX, y:canvasY}
	}

	function handleMouseDown(e) {
        // get canvasXY of click

		var canvasOffset = $("#linking").offset();
        var offsetX = canvasOffset.left;
        var offsetY = canvasOffset.top;
        canvasMouseX = parseInt(e.clientX - offsetX) + scrollX;
        canvasMouseY = parseInt(e.clientY - offsetY) + scrollY;
        // console.log(e.clientX, e.clientY);
        // console.log(offsetX, offsetY);
        // console.log(canvasMouseX, canvasMouseY);
        // console.log(relMouseCoords(e));
        for (var i = 0; i < clickingDot.length; i++) {
        	// console.log(clickingDot[i]);
   		 	var dx = canvasMouseX - clickingDot[i].x;
            var dy = canvasMouseY - clickingDot[i].y;
            if ((dx * dx + dy * dy) < (clickingDot[i].radius * clickingDot[i].radius)) {
                clickDot(clickingDot[i]);
            }
        }
    }

	$(document).ready(function(){
		var top = 30;
		var max = left.length * 40;
		var y = 20;

		c.height = max;
		c.width = 210;
		// console.log(left);
		for(var answer in left){
			// ctx.font = "30px Arial";
			// ctx.fillText(left[answer], 10, top);
			$('#left').append('<p style="margin: 0;padding: 11px 0 11px 0; text-align:center; font-size:14px;">'+left[answer]+'</p>');
			ctx.beginPath();
			ctx.arc(30, top-10, 5, 0, 2 * Math.PI, false);
			clickingDot.push({
				x: 30,
				y: top-10,
				radius: 5,
				text: left[answer],
				position: "left"
			});
			ref.left[left[answer]] = {
				x: 30,
				y: top-10,
				radius: 5,
				text: left[answer],
				position: "left"
			};
			ctx.fill();
			top += 40;
		}
		top = 30;
		// console.log(right);

		for(var answer in right){
			// ctx.font = "30px Arial";
			// ctx.fillText(right[answer], 400, top);
			$('#right').append('<p style="margin: 0;padding: 11px 0 11px 0; text-align:center; font-size:14px;">'+right[answer]+'</p>');
			ctx.beginPath();
			ctx.arc(200, top-10, 5, 0, 2 * Math.PI, false);
			clickingDot.push({
				x: 200,
				y: top-10,
				radius: 5,
				text: right[answer],
				position: "right"
			});
			ref.right[right[answer]] = {
				x: 200,
				y: top-10,
				radius: 5,
				text: right[answer],
				position: "right"
			};
			ctx.fill();
			top += 40;
		}
		// console.log(ref);
		// for(var i in history_map){
		// 	var pair = history_map[i].split("|:|");
		// 	console.log(pair);
		// 	console.log(ref.left[pair[0]], ref.right[pair[1]])
		// }
		// console.log(history_map);
		$("#linking").mousedown(function(e){
			handleMouseDown(e);
		});
		$('.submit').on('click', function(e){
			e.preventDefault();
			if(links.length == 0){
				alert("請回答問題");
				return false;
			}
			var qid = <?php echo $q->get_id(); ?>;
			var qtype = '<?php echo $q->get_type(); ?>';

			// console.log(links);

			$.ajax({
				url: '/ajax.php',
				type: 'POST',
				data: {
					type: 'checkAnswer',
					qid: qid,
					qtype: qtype,
					qsid: <?php echo $qs->get_id();?>,
					uid: <?php echo $user->get_profile()->id; ?>,
					answer: links
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
	});

</script>