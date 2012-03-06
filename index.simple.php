<?php
require('scripts/S3.php');
include ('settings.php');
error_reporting(0);
$gyazo_url = 'http://'.$_SERVER['SERVER_NAME']; 
//Bitly credentials
$pic=$_GET['pic'];

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
// If you use CloudFront change settings:
//	    $imageu=$aws_site.$aws_bucket.'/'.$name;    //we use CloudFront
	    $imageu=$cloudfront_site.'/'.$name;			
	//Output file url
		if (!$pic){ echo $gyazo_url."?limage=$imageu";}
		 // if index.php?pic=1 in client is set it will return shorted link
		elseif($pic==1){ echo $shorten = get_bitly_short_url($imageu,$bitly_name,$bitly_key);}
		 // if index.php?pic=2 in client is set it will return full link
		elseif($pic==2){ echo $imageu ;}
	}
}else {



$limage=$_GET['limage'];
$shorten =  get_bitly_short_url($limage,$bitly_name,$bitly_key);
echo "<head>
<link rel='shortcut icon' href=$limage>
<link rel='stylesheet' href='styles/style.css'>
</head>


<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>
<script src='scripts/jquery.zclip.min.js'></script>
<script type='text/javascript' src='scripts/copy.js'></script>

<span class='wrap'><input id='url1' type='text' value=$shorten> <span class='copy' id='copy1'></span></span>
<hr />
<img src=$limage>";



} 

?>
