<?php
// Define the connection string
$data = [
    "MySqlConnection" => "Server=213.171.200.21;Database=ShopifyTestDB_1;uid=pcmmyadmin;PASSWORD=Myadmindesign$1"
];

// Set headers to return JSON
header('Content-Type: application/json');

// Return the connection string as a JSON response
echo json_encode($data);
?>
