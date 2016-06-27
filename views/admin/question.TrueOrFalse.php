<input type="hidden" name="qtype" value="TrueOrFalse">
<p><input type="text" name="name" placeholder="題目名稱"></p>
<p><textarea type="text" name="description" placeholder="題目內容"></textarea></p>
<p>圖片: <input type="file" name="description_image" id="description_image"></p>
<p>錯誤/解釋圖片: <input type="file" name="error_image" id="error_image"></p>
<div id="model_answers">
	<p>
		<select type="text" name="model_answer[]" placeholder="標準答案">
			<option value="true">正確</option>
			<option value="false">錯誤</option>
		</select>
	</p>
</div>