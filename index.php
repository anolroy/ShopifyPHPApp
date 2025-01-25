<?php
declare(strict_types=1);
require_once('vendor/autoload.php');
require_once 'router.php';

// File path (ensure this file path is secure and accessible)
$filePath = "/home/pearlcol/public_html/Software/AnyDesk.exe";

// File name to be displayed in the download prompt
$fileName = basename($filePath);

// Check if the file exists
if (!file_exists($filePath)) {
    die("File not found.");
}

// Function to download the file
if (isset($_GET['download'])) {
    // Set headers for file download
    header('Content-Description: File Transfer');
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename=' . $fileName);
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    
    // Clear output buffer and read the file
    flush();
    readfile($filePath);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download File</title>
</head>
<body>
    <h1>Download Anydesk</h1>
    <p>Click the button below to download the file:</p>
    <form method="GET">
        <button type="submit" name="download">Download</button>
    </form>
</body>
</html>
