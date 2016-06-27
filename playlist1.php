<?php session_start();?>
<?php include 'views/header.php' ?>

<style>
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
<script>
    function deleteAllCookies() {
    var cookies = document.cookie.split(";");

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        var eqPos = cookie.indexOf("=");
        var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
        window.location = "./index.php";

    }
}
</script>

<div style="height:400px;">
<video id="videoarea" controls="controls" poster="" src="" style="height:350px;"></video>

<img id="imagearea" src="" style="width:540px;height:350px;"></img>
<ul id="playlist" style="height:300px; overflow-y: scroll;"> 
    <li movieurl="http://html5videoformatconverter.com/data/images/happyfit2.mp4" moviesposter="http://html5videoformatconverter.com/data/images/screen.jpg" alt='1'><img src="video/thumbnail_1.jpg" style="width:150px;">Happy Fit</li>
    <li movieurl="http://grochtdreis.de/fuer-jsfiddle/video/sintel_trailer-480.mp4" alt='0'><img src="video/thumbnail_2.jpg" style="width:150px;">Sintel</li>          
    <li movieurl="http://www.ioncannon.net/examples/vp8-webm/big_buck_bunny_480p.webm" alt="1"><img src="video/thumbnail_3.jpg" style="width:150px;">Big Buck Bunny</li>
    <li movieurl="http://hwcdn.libsyn.com/p/8/1/a/81a86c4cd0053917/Power_English_Update.mp3?c_id=1941078&expiration=1463717309&hwt=3db170653a39fedecf4118c081965aaa" alt="2" imgsrc="img/1.jpg`img/2.jpg`img/3.jpg" time=""><img src="video/thumbnail_4.jpg" style="width:150px;">ENGLISH LEARNING</li>
    <li movieurl="http://html5videoformatconverter.com/data/images/happyfit2.mp4" moviesposter="http://html5videoformatconverter.com/data/images/screen.jpg" alt='1'><img src="video/thumbnail_1.jpg" style="width:150px;">Happy Fit</li>
    <li movieurl="http://grochtdreis.de/fuer-jsfiddle/video/sintel_trailer-480.mp4" alt='0'><img src="video/thumbnail_2.jpg" style="width:150px;">Sintel</li>          
    <li movieurl="http://www.ioncannon.net/examples/vp8-webm/big_buck_bunny_480p.webm" alt="1"><img src="video/thumbnail_3.jpg" style="width:150px;">Big Buck Bunny</li>
    <li movieurl="http://hwcdn.libsyn.com/p/8/1/a/81a86c4cd0053917/Power_English_Update.mp3?c_id=1941078&expiration=1463717309&hwt=3db170653a39fedecf4118c081965aaa" alt="2" imgsrc="img/1.jpg`img/2.jpg`img/3.jpg" time=""><img src="video/thumbnail_4.jpg" style="width:150px;">ENGLISH LEARNING</li><li movieurl="http://html5videoformatconverter.com/data/images/happyfit2.mp4" moviesposter="http://html5videoformatconverter.com/data/images/screen.jpg" alt='1'><img src="video/thumbnail_1.jpg" style="width:150px;">Happy Fit</li>
    <li movieurl="http://grochtdreis.de/fuer-jsfiddle/video/sintel_trailer-480.mp4" alt='0'><img src="video/thumbnail_2.jpg" style="width:150px;">Sintel</li>          
    <li movieurl="http://www.ioncannon.net/examples/vp8-webm/big_buck_bunny_480p.webm" alt="1"><img src="video/thumbnail_3.jpg" style="width:150px;">Big Buck Bunny</li>
    <li movieurl="http://hwcdn.libsyn.com/p/8/1/a/81a86c4cd0053917/Power_English_Update.mp3?c_id=1941078&expiration=1463717309&hwt=3db170653a39fedecf4118c081965aaa" alt="2" imgsrc="img/1.jpg`img/2.jpg`img/3.jpg" time=""><img src="video/thumbnail_4.jpg" style="width:150px;">ENGLISH LEARNING</li>
</ul>
<audio id="audioarea" src="" controls style="width:400px"></audio>

</div>
<script>
var a=1;
var img = "";

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
        }else{
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
            for(var g=0;g<3;g++){
                img = str[g];
                setTimeout("changeimg('"+str[g]+"')",7000*g);
        }
     }  
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

<?php include 'views/footer.php' ?>