<input type="hidden" name="qtype" value="Punctuation">
<p><input type="text" name="name" placeholder="題目名稱"></p>
<p><textarea type="text" name="description" placeholder="題目內容"></textarea></p>
<p>圖片: <input type="file" name="description_image" id="description_image"></p>
<p>錯誤/解釋圖片: <input type="file" name="error_image" id="error_image"></p>
<div id="model_answers">
	<p>請輸入標點符號:</p>
	<p><input id="punctuation" type="text" name="answer"></p>
	<p>請輸入完整文章連標點符號:</p>
	<p><textarea id="answer" type="text" name="model_answer" placeholder="標準答案"></textarea></p>
	<p>預覽</p>
	<div id="preview"></div>
</div>
<script type="text/javascript">
	$('#answer').on('change', function(){
		var punct = $('#punctuation').val().split('');
		var punct_select = '<select>';
		for(var i in punct)
			punct_select += '<option>'+punct[i]+'</option>';
		punct_select += '</select>';
		var article = $(this).val();
		var output = '';
		var next = 0;
		for(var i = 0; i < article.length; i++){
			if(punct.indexOf(article[i]) != -1){
				output += article.slice(next, i);
				console.log(output);
				output += ' ' + punct_select + ' ';
				next = i+1;
			}
		}
		if(next != i.length)
			output += article.slice(next, i.length);
		$('#preview').html(output);
	});
</script>

