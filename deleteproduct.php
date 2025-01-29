<?php
include_once 'credentials.php';

// Check if the product ID or variant ID is provided
if (isset($_GET['variant_id'])) {
    $variantID = $_GET['variant_id'];

    // Create a connection to the database
    $conn = new mysqli($servername, $username, $password, $database);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL statement to delete the product
    $sql = "DELETE FROM stockrecords WHERE variant_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $variantID);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Product with Variant ID $variantID deleted successfully.";
    } else {
        echo "Error deleting product: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "No variant ID provided.";
}
?>
