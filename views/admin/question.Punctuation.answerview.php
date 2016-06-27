<?php 
$ma = $q->getModelAnswer(); 
$a = $q->getAnswers();
// var_dump($ma);
$type = array('description_image'=>'圖片', 'error_image'=>'錯誤/解釋圖片');
?>
<b>題目內容</b>
<p><?php echo $q->getQuestion(); ?></p>
<b>標點符號</b>
<p><?php echo $a->answer; ?></p>
<b>標準答案</b>
<ul>
<?php echo $ma->answer; ?>
</ul>
<b>預覽</b>
<div>
	<?php $punct = preg_split('//u', $a->answer, -1, PREG_SPLIT_NO_EMPTY); ?>
	<?php 
		$selector = ' <select>';
		foreach($punct as $p){
			$selector .= '<option value="'.$p.'">'.$p.'</option>';
		}
		$selector .= '</select> ';
	?>
	<?php for($i = 0; $i < mb_strlen($ma->answer, 'utf-8'); $i++): ?>
		<?php $chr = mb_substr($ma->answer, $i, 1, 'utf-8'); ?>
		<?php if(array_search($chr, $punct) !== false): ?>
			<?php echo $selector; ?>
		<?php else: ?>
			<?php echo $chr; ?>
		<?php endif; ?>
	<?php endfor; ?>
</div>

<?php foreach($q->getImage() as $image): ?>
<div style="display: inline-block; width: 200px; vertical-align: top;">
	<p><?php echo $type[$image['type']]; ?></p>
	<img style="max-width: 200px" src="/uploads/<?php echo $image['id'];?>.<?php echo $image['ext']; ?>">
</div>
<?php endforeach; ?>