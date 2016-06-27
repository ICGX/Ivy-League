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
<?php $lectures = Lecture::getSequenceBySubjectGrade($_POST['subject'], $_POST['grade']); ?>
<form action="admin_action.php" method="POST">
	<input type="hidden" name="type" value="removeSequence">
	<table>
		<?php if($lectures) foreach ($lectures as $lecture) : ?>
			<tr>	
				<?php 
					$l = new Lecture($lecture['id'], 'sequence'); 
					$image = $l->getThumbnail();
				?>
				<td style="width: 20%">
					<p>預覽圖片</p>
					<img style="width: 100%" src="<?php echo '/uploads/'.$image->id.'.'.$image->ext; ?>" alt="thumbnail">
				</td>
				<?php 
					$seq = $l->getSequence();
					$music = $seq['music'];
					$seq = $seq['images'];
					$img_seq = array();
					foreach($seq as $s){
						array_push($img_seq, '/uploads/'.$s['id'].'.'.$s['ext']);
					}
				?>
				<td style="width: 40%">
					<video id="video-<?php echo $lecture['id']; ?>" poster='<?php echo $img_seq[0]; ?>' width="400"></video>
					<audio id="audio-<?php echo $lecture['id']; ?>" src="/uploads/m.<?php echo $music->id.'.'.$music->ext;?>"></audio>
				</td>
				<td>
					<p><?php echo $lecture['name'];?></p>
					<p><button id="<?php echo $lecture['id']; ?>" data-imgs="<?php echo implode('`', $img_seq);?>" class="play" data-intvs="<?php echo $lecture['interval'];?>">播放影片</button></p>
					<p><button name="id" value="<?php echo $lecture['id']; ?>">刪除影片</button></p>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</form>
<a href="/admin/index.php">返回</a>
<script type="text/javascript">
	$('.play').on('click', function(e){
		e.preventDefault();
		console.log("play");
		var self = e.currentTarget.id;
		document.getElementById('audio-'+self).play();
        str = e.currentTarget.dataset.imgs.split('`');
        inv = e.currentTarget.dataset.intvs.split('`');
        var accu = 0;
        for(var g=0;g<str.length;g++){
            img = str[g];
            accu += inv[g] * 1000;
            setTimeout("changeimg('"+str[g]+"','video-"+self+"')", accu);
        }
	});
	function changeimg(e, id){
		$('#'+id).attr('poster', e);
	}
</script>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php'; ?>