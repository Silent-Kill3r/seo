<?php
// Set the root directory path (this can be customized to your need)
$rootDir = __DIR__;

// Define a pattern to search for obfuscated strings (you can adjust this regex pattern based on your encoding format)
$pattern = '/\/\*[\w\|\+\=\/\-\.\,\(\)]+\/\*/'; // This pattern is based on your given example

// Create empty arrays to store file URLs
$encodedFiles = [];
$octetStreamFiles = [];

// Function to scan directory and return files that match the encoding pattern
function scanEncodedFiles($dir, $pattern) {
    global $encodedFiles;

    // Check if the directory exists
    if (!is_dir($dir)) {
        return;
    }

    // Open the directory
    $files = scandir($dir);

    // Loop through the files
    foreach ($files as $file) {
        // Skip '.' and '..' directories
        if ($file == '.' || $file == '..') {
            continue;
        }

        // Get the full path of the file
        $fullPath = $dir . DIRECTORY_SEPARATOR . $file;

        // If the file is a directory, call the function recursively
        if (is_dir($fullPath)) {
            scanEncodedFiles($fullPath, $pattern);
        } else {
            // If the file is a PHP file, read the file content and search for the pattern
            if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
                $fileContent = file_get_contents($fullPath);

                // Search for the pattern in the file content
                if (preg_match($pattern, $fileContent)) {
                    // If a match is found, store the file URL
                    $fileUrl = $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', $fullPath);
                    $encodedFiles[] = $fileUrl;
                }
            }
        }
    }
}

// Function to scan directory and return files of type application/octet-stream
function scanOctetStreamFiles($dir) {
    global $octetStreamFiles;

    // Check if the directory exists
    if (!is_dir($dir)) {
        return;
    }

    // Open the directory
    $files = scandir($dir);

    // Loop through the files
    foreach ($files as $file) {
        // Skip '.' and '..' directories
        if ($file == '.' || $file == '..') {
            continue;
        }

        // Get the full path of the file
        $fullPath = $dir . DIRECTORY_SEPARATOR . $file;

        // If the file is a directory, call the function recursively
        if (is_dir($fullPath)) {
            scanOctetStreamFiles($fullPath);
        } else {
            // Check the MIME type of the file
            $mimeType = mime_content_type($fullPath);

            // If the MIME type is application/octet-stream, store the file URL
            if ($mimeType == 'application/octet-stream') {
                $fileUrl = $_SERVER['HTTP_HOST'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', $fullPath);
                $octetStreamFiles[] = $fileUrl;
            }
        }
    }
}

// Call both functions to scan the files
scanEncodedFiles($rootDir, $pattern);
scanOctetStreamFiles($rootDir);

// Output the result for encoded files
if (count($encodedFiles) > 0) {
    echo "<h2>PHP Files with Encoded Content:</h2>";
    echo "<ul>";
    foreach ($encodedFiles as $fileUrl) {
        echo "<li><a href='http://$fileUrl'>$fileUrl</a></li>";
    }
    echo "</ul>";
} else {
    echo "<p>No encoded files were found.</p>";
}

// Output the result for files with MIME type 'application/octet-stream'
if (count($octetStreamFiles) > 0) {
    echo "<h2>Files with MIME Type 'application/octet-stream':</h2>";
    echo "<ul>";
    foreach ($octetStreamFiles as $fileUrl) {
        echo "<li><a href='http://$fileUrl'>$fileUrl</a></li>";
    }
    echo "</ul>";
} else {
    echo "<p>No files with MIME type 'application/octet-stream' were found.</p>";
}
?>
