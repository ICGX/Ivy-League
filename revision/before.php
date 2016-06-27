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
if(!isset($_GET['qsid'])) header('Location: /revision/question_set.php');
require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.questionset.php';

$user = new User($_SESSION['id']);
$qs = new QuestionSet($_GET['qsid']);
$attempt_record = $qs->get_attempt($_SESSION['id']);
$day = null;
if($attempt_record) $day = date("Y-m-d", strtotime($attempt_record->lastsubmit));
$today = date("Y-m-d", time());
?>

<h1><?php echo $qs->get_name(); ?></h1>
<blockquote>Instruction......</blockquote>
<?php $count = count($qs->get_questions()); ?>
<?php $attempt = $user->get_attempt($qs->get_id()); ?>
<p>此題目共有 <?php echo $count; ?> 條問題</p>
<?php if($attempt): ?>
	<?php if($attempt->attempt_count > 0): ?>
		<p>你已回答 <?php echo $attempt->attempt_count; ?> 次</p>
	<?php else: ?>
		<p>你已經開始這個練習</p>
	<?php endif; ?>
<?php else: ?>
	<p>你還未嘗試這個練習</p>
<?php endif; ?>
<?php if($count > 0): ?>
	<?php if($attempt && $attempt->finish == 1): ?>
		<p>你已經完成此練習</p>
		<a href="/revision.php">返回</a>
	<?php elseif($day == $today): ?>
		<h3>你今天已經做過一次，重答已作答的題目不會獲得分數！</h3>
		<a href="/revision/question_set.php?qsid=<?php echo $_GET['qsid']; ?>&page=1">開始</a>
		<a href="/revision.php">返回</a>
	<?php else: ?>
		<a href="/revision/question_set.php?qsid=<?php echo $_GET['qsid']; ?>&page=1">開始</a>
		<a href="/revision.php">返回</a>
		<?php $qs_attempt = $qs->get_questions_attempts(); ?>
		<?php //var_dump($qs_attempt); ?>
		<?php if($qs_attempt): ?>
			<?php //var_dump($qs_attempt[0]); ?>
		<?php endif; ?>
	<?php endif; ?>
<?php else: ?>
	<a href="/revision.php">返回</a>
<?php endif; ?>

<?php require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php'; ?>

