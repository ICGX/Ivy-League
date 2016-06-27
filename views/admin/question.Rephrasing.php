<input type="hidden" name="qtype" value="Rephrasing">
<p><input type="text" name="name" placeholder="題目名稱"></p>
<p><textarea type="text" name="description" placeholder="題目內容"></textarea></p>
<p>圖片: <input type="file" name="description_image" id="description_image"></p>
<p>錯誤/解釋圖片: <input type="file" name="error_image" id="error_image"></p>
<div id="model_answers">
	<h3>標準答案</h3>
	<p>1: <input type="text" id="model_answer_0" placeholder="答案"><button class="add">＋</button></p>
</div>
<script type="text/javascript">
	var linking = 1;
	var addAnswer = function(e){
		e.preventDefault();
		$('.add').off('click', addAnswer);
		$('#model_answers').append('<p>'+(linking+1)+': <input type="text" id="model_answer_'+linking+'" placeholder="答案/句子部分"><button class="add">＋</button></p>');
		linking++;
		$('.add').on('click', addAnswer);
	};
	$('.add').on('click', addAnswer);
	var maddAnswer = function(e){
		e.preventDefault();
		$('.madd').off('click', maddAnswer);
		$('#answers').append('<p><input type="text" name="answer[]" placeholder="非答案"><button class="madd">＋</button></p>');
		$('.madd').on('click', maddAnswer);
	};
	$('.madd').on('click', maddAnswer);
	$('#question').submit(function(e){
		var answers = [];
		for(var i = 0; i < linking; i++){
			answers.push(i+'|:|'+$('#model_answer_'+i).val());
		}
		for(var i in answers){
			$('#model_answers').append('<input type="hidden" name="model_answer[]" value="'+answers[i]+'">');
		}
		console.log(pairs);
		// return false;
	});
</script>