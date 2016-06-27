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

date_default_timezone_set('Asia/Hong_Kong');
?>

<?php
$qsid = $_GET['qsid'];
$page = isset($_GET['page'])?$_GET['page']:1;
$user = new User($_SESSION['id']);
$qs = new QuestionSet($qsid);
$page_count = $qs->get_questions_count();
$q = $qs->get_question($page-1);
$attempt_record = $qs->get_attempt($_SESSION['id']);
$attempt_record = null;
$day = null;
if($attempt_record) $day = date("d", strtotime($attempt_record->lastsubmit));
$today = date("d", time());
if($day == $today) header('Location: /revision/before.php?qsid='.$qsid);
// var_dump($q);
require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';

$attempt = $user->get_attempt($qsid);
if(!$attempt){
	$qs->started($_SESSION['id']);
}
$history = Question::getAttemptHistory($_SESSION['id'], $q->qid);
// var_dump($history);

?>
<h1><?php echo $qs->get_name(); ?></h1>
<blockquote><?php echo $qs->get_description(); ?></blockquote>

<?php include $_SERVER['DOCUMENT_ROOT'].'/views/revision/revision.'.$q->type.'.php'; ?>

<div style="width: 100%">
<?php if($page > 1): ?>
	<a class="prev" href="/revision/question_set.php?qsid=<?php echo $qsid; ?>&page=<?php echo $page-1; ?>"><img src="/image/btn_previous.png"></a>
<?php endif; ?>

<?php //if(!$history || $history->correct != '1'): ?>
	<a class="submit" href="#"><img src="/image/btn_submit.png"></a>
<?php //endif; ?>

<?php if($page < $page_count): ?>
	<a class="next" href="/revision/question_set.php?qsid=<?php echo $qsid; ?>&page=<?php echo $page+1; ?>"><img src="/image/btn_next.png"></a>
<?php else: ?>
	<a class="next" href="/revision/finish.php?qsid=<?php echo $qsid; ?>&page=<?php echo $page+1; ?>"><img src="/image/btn_next.png"></a>
<?php endif; ?>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php'; ?>