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
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/views/header.php' ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/models/class.lecture.php'; ?>

<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;}
.tg .tg-yw4l{vertical-align:top}

#playlist {
    display:table;
}
#playlist li{
    cursor:pointer;
    padding:8px;
}

#playlist li:hover{
    color:blue;                        
}
#videoarea, #imagearea {
    float:left;
    width:640px;
    height:480px;
    margin:10px;    
    border:0px solid silver;
}
li {
    list-style-type: none;
}

</style>

<h2>教學影片 - <?php echo base64_decode($_GET['name']); ?></h2>
<?php $user = new User($_SESSION['id']); ?>
<?php $subject = $_GET['subject']; ?>
<?php $lecture = new Lecture();?>
<?php $videos = $lecture->getVideoBySubjectGrade($subject, $user->get_grade()->grade); ?>
<?php $sequence = $lecture->getSequenceBySubjectGrade($subject, $user->get_grade()->grade); ?>

<?php if(count($videos) + count($sequence) > 0): ?>
<div style="height:400px;">
	<video id="videoarea" controls="controls" poster="" src="" style="height:350px;"></video>
	<img id="imagearea" src="" style="width:540px;height:350px;"></img>
	<div style="height:100%; overflow-y: scroll;">
		<ul id="playlist">
		<?php if($videos): ?>
			<?php foreach($videos as $v): ?>
				<?php 
				$l = new Lecture($v['vid']);
				$thumbnail = $l->getThumbnail();
				?>
				<li movieurl="<?php echo Lecture::getBaseUrl().$v['location'];?>" moviesposter="/uploads/<?php echo $thumbnail->id;?>.<?php echo $thumbnail->ext;?>" alt='1' data-lid="<?php echo $l->getLectureId();?>" data-uid="<?php echo $_SESSION['id']; ?>"><img src="/uploads/<?php echo $thumbnail->id;?>.<?php echo $thumbnail->ext;?>" style="width:150px;"><?php echo $v['name'];?></li>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if($sequence): ?>
			<?php foreach($sequence as $s): ?>
				<?php 
				$l = new Lecture($s['id'], 'sequence');
				$name = $s['name'];
				$thumbnail = $l->getThumbnail();
				$seq = $l->getSequence();
				$music = $seq['music'];
				$seq = $seq['images'];
				$img_seq = array();
				foreach($seq as $se){
					array_push($img_seq, '/uploads/'.$se['id'].'.'.$se['ext']);
				}
				$l->refreshLid(true);
				?>
				<li movieurl="/uploads/m.<?php echo $music->id.'.'.$music->ext;?>" moviesposter="/uploads/<?php echo $thumbnail->id;?>.<?php echo $thumbnail->ext;?>" alt='2' imgsrc="<?php echo implode('`', $img_seq);?>" data-intvs="<?php echo $s['interval'];?>" data-lid="<?php echo $l->getLectureId();?>" data-uid="<?php echo $_SESSION['id']; ?>"><img src="/uploads/<?php echo $thumbnail->id;?>.<?php echo $thumbnail->ext;?>" style="width:150px;"><?php echo $name;?></li>
			<?php endforeach; ?>
		<?php endif; ?>
		</ul>
	</div>
	<audio id="audioarea" src="" controls style="width:400px"></audio>
</div>

<script type="text/javascript">
	$(function() {
	    $("#videoarea").show();
	    $("#audioarea").hide();
	    $('#imagearea').hide();
	    $("#playlist li").on("click", function() {
	        //alert($(this).attr("alt"));
	        if($(this).attr("alt")!=2){
		        $("#videoarea").attr({
		            "src": $(this).attr("movieurl"),
		            "poster": "",
		            "autoplay": "autoplay"
		        });
		        $("#audioarea").hide();
		        $("#videoarea").show();
		        $('#imagearea').hide();
		        $("#audioarea").trigger("pause");
		        document.getElementById("audioarea").currnetTime = 0;
	        }
	        else{
		        $("#audioarea").attr({
		            "src": $(this).attr("movieurl"),
		            "autoplay": "autoplay"
		        });
		        $("#videoarea").hide();
		        $("#audioarea").show();
		        $('#imagearea').show();
		        $("#videoarea").trigger("pause");
		        document.getElementById("videoarea").currentTime = 0;

		        var str = $(this).attr("imgsrc");
		        str = str.split('`');
		        inv = this.dataset.intvs.split('`');
		        var accu = 0;
		        changeimg(str[0], "video-"+self);
		        for(var g=0;g<str.length;g++){
		            img = str[g];
		            accu += inv[g] * 1000;
		            setTimeout("changeimg('"+str[g]+"','video-"+self+"')", accu);
		        }
		    }
		    $.ajax({
		    	url: '/ajax.php',
		    	type: 'POST',
		    	data: {
		    		type: 'watchLecture',
		    		uid: this.dataset.uid,
		    		lid: this.dataset.lid
		    	}
		    }).done(function(){});
	    });
	    $('#videoarea').on('ended',function(){
	      $("#videoarea").attr({
	            "src": $("#playlist li").eq(a).attr("movieurl"),
	            "poster": "",
	            "autoplay": "autoplay"
	        });
	      a++;
	    });
	 
	    $("#videoarea").attr({
	        "src": $("#playlist li").eq(0).attr("movieurl"),
	        "poster": $("#playlist li").eq(0).attr("moviesposter")
	    })
	})

	function changeimg(e){
	    document.getElementById("imagearea").src=e;
	}
</script>
<?php else: ?>
	<h2>暫無影片</h2>
<?php endif; ?>

<?php require_once $_SERVER['DOCUMENT_ROOT'].'/views/footer.php' ?>