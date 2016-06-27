<?php 
$ma = $q->getModelAnswer(); 
$a = $q->getAnswers();
// var_dump($ma);
$type = array('description_image'=>'圖片', 'error_image'=>'錯誤/解釋圖片');
?>
<b>題目內容</b>
<p><?php echo $q->getQuestion(); ?></p>
<b>標準答案</b>
<ul>
<?php foreach($ma as $answer): ?>
	<li><?php echo $answer['answer']; ?></li>
<?php endforeach; ?>
</ul>
<b>其他答案</b>
<ul>
<?php foreach($a as $answer): ?>
	<li><?php echo $answer['answer']; ?></li>
<?php endforeach; ?>
</ul>

<?php foreach($q->getImage() as $image): ?>
<div style="display: inline-block; width: 200px; vertical-align: top;">
	<p><?php echo $type[$image['type']]; ?></p>
	<img style="max-width: 200px" src="/uploads/<?php echo $image['id'];?>.<?php echo $image['ext']; ?>">
</div>
<?php endforeach; ?>