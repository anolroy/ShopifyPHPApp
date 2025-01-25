<?php

// JSON data
$data = [
    [
        "name" => "John Doe",
        "email" => "john.doe@example.com",
        "age" => 30,
        "country" => "USA",
    ],
    [
        "name" => "Jane Smith",
        "email" => "jane.smith@example.com",
        "age" => 25,
        "country" => "UK",
    ],
];


// Encode the JSON data
$jsonData = json_encode($data);

// URL to send the data to
$url = "page2.php?data=" . urlencode($jsonData);

// Redirect to the next page with the JSON data
header("Location: $url");
exit;
?>
<?php
// Check if 'data' is present in the GET request
if (isset($_GET['data'])) {
    // Decode the JSON data
    $jsonData = urldecode($_GET['data']);
    $data = json_decode($jsonData, true);

    // Process the data
    foreach ($data as $user) {
        echo "Name: " . $user["name"] . "<br>";
        echo "Email: " . $user["email"] . "<br>";
        echo "Age: " . $user["age"] . "<br>";
        echo "Country: " . $user["country"] . "<br>";
        echo "<hr>";
    }
} else {
    echo "No data received.";
}
?>
