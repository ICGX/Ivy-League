<input type="hidden" name="qtype" value="MultipleChoice">
<p><input type="text" name="name" placeholder="題目名稱"></p>
<p><textarea type="text" name="description" placeholder="題目內容"></textarea></p>
<p>圖片: <input type="file" name="description_image" id="description_image"></p>
<p>錯誤/解釋圖片: <input type="file" name="error_image" id="error_image"></p>
<div id="model_answers">
	<p><input type="text" name="model_answer[]" placeholder="標準答案"></p>
</div>
<div id="answers">
	<p><input type="text" name="answer[]" placeholder="答案"><button class="add">＋</button></p>
</div>
<script type="text/javascript">
	var addAnswer = function(e){
		e.preventDefault();
		$('.add').off('click', addAnswer);
		$('#answers').append('<p><input type="text" name="answer[]" placeholder="答案"><button class="add">＋</button></p>');
		$('.add').on('click', addAnswer);
	};
	$('.add').on('click', addAnswer);
	var maddAnswer = function(e){
		e.preventDefault();
		$('.madd').off('click', maddAnswer);
		$('#model_answers').append('<p><input type="text" name="model_answer[]" placeholder="標準答案"><button class="madd">＋</button></p>');
		$('.madd').on('click', maddAnswer);
	};
	$('.madd').on('click', maddAnswer);
</script>