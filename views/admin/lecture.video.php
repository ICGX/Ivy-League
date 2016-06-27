<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.accesscontrol.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.lecture.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.user.php';
$course_admin = new AccessControl('course_admin');
$user = new User($_SESSION['id']);
if(!$course_admin->hasReadAccess($user)){
	header('Location: /prohibited.php');
}
require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php';
?>

<h2>檢視／修改影片</h2>
<?php $lectures = Lecture::getVideoBySubjectGrade($_POST['subject'], $_POST['grade']); ?>
<form action="admin_action.php" method="POST">
	<input type="hidden" name="type" value="removeVideo">
	<table>
		<tr>
			<?php if($lectures) foreach ($lectures as $lecture) : ?>
				<?php 
					$l = new Lecture($lecture['vid']); 
					$image = $l->getThumbnail();
				?>
				<td style="width: 20%">
					<p>預覽圖片</p>
					<img style="width: 100%" src="<?php echo '/uploads/'.$image->id.'.'.$image->ext; ?>" alt="thumbnail">
				</td>
				<td style="width: 40%">
					<video width="400" controls>
						<source src="<?php echo Lecture::getBaseUrl().$lecture["location"];?>">
					</video>
				</td>
				<td>
					<p><?php echo $lecture['name'];?></p>
					<p><button name="id" value="<?php echo $lecture['vid']; ?>">刪除影片</button></p>
				</td>
			<?php endforeach; ?>
		</tr>
	</table>
</form>
<a href="/admin/index.php">返回</a>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php'; ?>