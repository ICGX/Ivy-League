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
?>
<?php require_once 'views/header.php' ?>

<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;}
.tg .tg-yw4l{vertical-align:top}
</style>

<h2>教學</h2>
<?php $user = new User($_SESSION['id']); ?>
<?php $subjects = User::get_subjects(); ?>
<?php $has_subject = array(); ?>
<?php
foreach($user->get_subject() as $s){
	array_push($has_subject, $s['name']);
}
?>
<?php foreach($subjects as $subject): ?>
	<?php if(array_search($subject['name'], $has_subject) === false) continue; ?>
	<p><a href="/lecture/gallery.php?subject=<?php echo $subject['id']; ?>&name=<?php echo base64_encode($subject['name']); ?>"><?php echo $subject['name']; ?></a></p>
<?php endforeach; ?>

<?php include 'views/footer.php' ?>