<?php 
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
if(!isset($_SESSION['id'])){
	header('Location: /index.php');
}
$user = new User($_SESSION['id']);
if(!AccessControl::checkIP($_SERVER['REMOTE_ADDR'])){
	header('Location: /prohibited.php');
}
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.questionset.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';
?>

<?php
$qsid = $_GET['qsid'];
$page = isset($_GET['page'])?$_GET['page']:1;
$user = new User($_SESSION['id']);
$qs = new QuestionSet($qsid);
// var_dump($q);
// var_dump($history);

?>
<h1><?php echo $qs->get_name(); ?></h1>
<blockquote><?php echo $qs->get_description(); ?></blockquote>

<?php 
$attempt_detail = $qs->get_attempts_detail($_SESSION['id']);
if($attempt_detail['qs_attempt'] && $attempt_detail['qs_attempt']->finish)
	$finished = (int)$attempt_detail['qs_attempt']->finish;
else
	$finished = 0;
if(!$finished){
	$all = 1;
	// var_dump($attempt_detail['q_attempt']);
	foreach($attempt_detail['q_attempt'] as $attempt){
		// var_dump($attempt);
		if($attempt == NULL || $attempt->correct != '1')
			$all = 0;
	}
	$finished = $all;
}
$qttempted = $qs->attempt($_SESSION['id'], ($finished==1?0:1));
if($finished) $qs->finished($_SESSION['id']);
$attempt_detail['qs_attempt'] = $qs->get_attempt($_SESSION['id']);
// echo '<pre>';
// var_dump($attempt_detail);
// echo '</pre>';

$questions = $qs->get_questions();
// var_dump($questions);
?>
<?php if($finished): ?>
	<h2>你已經完成這個練習，並答對所有問題！</h2>
<?php else: ?>
	<h2>你已經完成練習，請留意適時重溫！</h2>
<?php endif;?>

<h3>問題：</h3>
<table>
<!-- <?php var_dump($attempt_detail['q_attempt']);?> -->
<?php $types = Question::get_types(); ?>
<?php $total = 0; ?>
<?php $correct = 0; ?>
<?php foreach ($attempt_detail['q_attempt'] as $id => $attempt): ?>
	<?php $total++ ;?>
	<?php if($attempt): ?>
		<tr>
			<?php if($attempt->correct) $correct++; ?>
			<td><?php echo $types[$attempt->type]; ?>:</td>
			<td><?php echo $attempt->name; ?></td>
			<td><?php echo $attempt->correct == '1'? '已答對':'未答對'; ?></td>
		</tr>
	<?php else: ?>
		<?php $type = Question::getType($id); ?>
		<?php $q = new $type($id); ?>
		<tr>
			<td><?php echo $types[$type]; ?>:</td>
			<td><?php echo $q->get_name(); ?></td>
			<td>未完成</td>
		</tr>
	<?php endif; ?>
	<?php //var_dump($types);?>
	<?php //var_dump(Question::getAttemptHistory($_SESSION['id'], $attempt->qid)); ?>
<?php endforeach; ?>
</table>

<?php
	// var_dump(expression);
$score = $qs->score($_SESSION['id'], ((double)$correct/(double)$total) * 100);
if($score[0]){
	echo "<p>你獲得{$score[1]}顆星星!</p>";
}else{
	echo "<p>你今天已獲得{$score[1]}顆星星, 重做並不會增加你的星星!</p>";
}
?>

<div style="width: 100%">
<?php if($page > 1 && false): ?>
	<a class="prev" href="/revision/question_set.php?qsid=<?php echo $qsid; ?>&page=<?php echo $page-1; ?>"><img src="/image/btn_previous.png"></a>
<?php endif; ?>

<a href="/revision.php">完成</a>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php'; ?>