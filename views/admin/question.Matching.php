<input type="hidden" name="qtype" value="Matching">
<p><input type="text" name="name" placeholder="題目名稱"></p>
<p><textarea type="text" name="description" placeholder="題目內容"></textarea></p>
<p>圖片: <input type="file" name="description_image" id="description_image"></p>
<p>錯誤/解釋圖片: <input type="file" name="error_image" id="error_image"></p>
<div id="model_answers">
	<h3>標準答案</h3>
	<p><input type="text" id="pair0_0" placeholder="答案"> ---- <input type="text" id="pair0_1" placeholder="答案"><button class="add">＋</button></p>
</div>
<div id="answers">
	<h3>非答案</h3>
	<p><input type="text" name="answer[]" placeholder="非答案"><button class="madd">＋</button></p>
</div>
<script type="text/javascript">
	var linking = 1;
	var addAnswer = function(e){
		e.preventDefault();
		$('.add').off('click', addAnswer);
		$('#model_answers').append('<p><input type="text" id="pair'+linking+'_0" placeholder="答案"> ---- <input type="text" id="pair'+linking+'_1" placeholder="答案"><button class="add">＋</button></p>');
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
		var pairs = [];
		for(var i = 0; i < linking; i++){
			pairs.push($('#pair'+i+'_0').val()+'|:|'+$('#pair'+i+'_1').val());
		}
		for(var i in pairs){
			$('#model_answers').append('<input type="hidden" name="model_answer[]" value="'+pairs[i]+'">');
		}
		console.log(pairs);
		// return false;
	});
</script>