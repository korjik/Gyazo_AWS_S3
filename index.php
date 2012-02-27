<?php
require('files/S3.php');


//Bitly credentials
$bitly_name='XXX';
$bitly_key='XXXX';


$gyazo_url = 'http://'.$_SERVER['SERVER_NAME']; 
$company_name='my_company';

//AWS Credentials
$aws_key='XXX';
$aws_secret='XXX';
$aws_bucket='bucket_name';
$aws_site='http://s3.amazonaws.com/';

/* returns the shortened url */
function get_bitly_short_url($url,$login,$appkey,$format='txt') {
  $connectURL = 'http://api.bit.ly/v3/shorten/?login='.$login.'&apiKey='.$appkey.'&uri='.urlencode($url).'&format='.$format;
  return curl_get_result($connectURL);
}


/* returns a result form url */
function curl_get_result($url) {
  $ch = curl_init();
  $timeout = 5;
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

//When file is uploaded

if(isset($_FILES['imagedata']['name'])) {
	$name = substr(md5(time()), -28).'.'.$company_name.'.png';
//Store it on s3
	if ($tempname = $_FILES['imagedata']['tmp_name']){
	    $s3 = new S3($aws_key,$aws_secret);
	    $s3->putBucket($aws_bucket, S3::ACL_PUBLIC_READ);
	    $s3->putObjectFile($tempname, $aws_bucket,$name, S3::ACL_PUBLIC_READ);
	    $imageu=$aws_site.$aws_bucket.'/'.$name;
	//Output file url
	    echo $gyazo_url."?limage=$imageu";
	}
}else {


$limage=$_GET['limage'];
$shorten =  get_bitly_short_url($limage,$bitly_name,$bitly_key);
echo "<head>
<link rel='shortcut icon' href=$limage>
</head>
<style type='text/css'>
#url1, #url2 {
        width:150px;
}
#url2 {
}
.copy {
        display:inline-block;
        height:16px;
        width:16px;
        background:url(files/copy.png);
        position:relative;
        top:3px;
}
.copy:hover {
        opacity:0.5;
        cursor:pointer;
}
.copied {
        display:inline-block;
        height:16px;
        width:16px;
        background:url(files/copied.png);
        position:relative;
        top:3px;
        margin-left:5px;
}
.wrap {
        display:block;
        width:300px;
}
</style>
<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>
<script src='files/jquery.zclip.min.js'></script>
<script type='text/javascript'>
$(document).ready(function(){
        $('#copy1').zclip({
                path:'files/ZeroClipboard.swf',
                copy:$('#url1').val(),
                afterCopy:function(){
                        $(this).after('<span class=copied></span>');
                        $(this).next('.copied').fadeOut('slow');
                },
        });
        $('#copy2').zclip({
                path:'files/ZeroClipboard.swf',
                copy:$('#url2').val(),
                afterCopy:function(){
                        $(this).after('<span class=copied></span>');
                        $(this).next('.copied').fadeOut('slow');
                },
        });
        $('#url1, #url2').click(function(){
                $(this).select();
        });
});
</script>

<span class='wrap'><input id='url1' type='text' value=$shorten> <span class='copy' id='copy1'></span></span>
<hr />
<img src=$limage>";



} 

?>
