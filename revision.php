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
require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.questionset.php';


$user = new User($_SESSION['id']);
// $qs = new QuestionSet($qsid);
?>

<?php $question_set_list = QuestionSet::get_question_set_list(); ?>
<?php $user_subject = $user->get_subject(); ?>
<?php 
$sub = array();
foreach($user_subject as $s){
	array_push($sub, $s['name']);
}
?>
<?php //var_dump($user_subject); ?>
<?php $user_grade = $user->get_grade()->grade; ?>
<?php
$subjects = array();
foreach($question_set_list as $qs){
	$qso = new QuestionSet($qs['qsid']);
	$s = $qso->get_subject();
	//var_dump($s);
	if(!isset($subjects[$s->name])) $subjects[$s->name] = array();
	array_push($subjects[$s->name], $qso);
}
// var_dump($subjects);
?>
<?php foreach($subjects as $name => $subject): ?>
	<?php if(array_search($name, $sub) === false) continue; ?>
	<h2><?php echo $name; ?></h2>
	<?php foreach($subject as $s): ?>
		<?php if($s->get_grade()->grade != $user_grade) continue; ?>
		<?php $attempt = $s->get_attempt($_SESSION['id']); ?>
		<?php //var_dump($attempt); ?>
		<?php $thumb = $s->getThumbnail(); ?>
		<p>
			<a href="/revision/before.php?qsid=<?php echo $s->get_id(); ?>">
				<?php if($thumb): ?>
					<img style="max-width:100%; max-height: 200px" src="/uploads/<?php echo $thumb->id; ?>.<?php echo $thumb->ext; ?>" alt="thumb">
				<?php endif; ?>
				<?php echo $s->get_name(); ?> <?php if($attempt && $attempt->finish == '1') echo ' (已完成)';?>
			</a>
		</p>
	<?php endforeach; ?>
<?php endforeach; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php'; ?>