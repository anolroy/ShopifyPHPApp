<?php
// Example JSON payload from a webhook
$json_payload = '
{
    "order_id": 12345,
    "customer": {
        "name": "John Doe",
        "email": "john.doe@example.com"
    },
    "items": [
        {"product_id": 1, "product_name": "Widget", "quantity": 2, "price": 19.99},
        {"product_id": 2, "product_name": "Gadget", "quantity": 1, "price": 29.99},
        {"product_id": 3, "product_name": "Thingamajig", "quantity": 3, "price": 9.99},
        {"product_id": 4, "product_name": "Doohickey", "quantity": 4, "price": 14.99},
        {"product_id": 5, "product_name": "Contraption", "quantity": 2, "price": 24.99},
        {"product_id": 6, "product_name": "Gizmo", "quantity": 1, "price": 39.99},
        {"product_id": 7, "product_name": "Apparatus", "quantity": 5, "price": 49.99}
    ],
    "total": 69.97,
    "status": "updated"
}
';

// Decode the JSON payload into a PHP array
$data = json_decode($json_payload, true);

// Check if JSON decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    die('Invalid JSON payload');
}

// Loop through the main object
foreach ($data as $key => $value) {
    //echo "Key: $key\n";
    
    // Check if the value is an array
    if (is_array($value)) {
        // If the key is 'items', loop through the array of items
        if ($key === 'items') {
            foreach ($value as $item) {
                foreach ($item as $item_key => $item_value) {
                    //echo "\t$item_key: $item_value<br>\n";
                }
            }
        } else {
            // Loop through the nested object (e.g., customer)
            foreach ($value as $sub_key => $sub_value) {
                echo "\t$sub_key: $sub_value<br>\n";
            }
        }
    } else {
        // If the value is not an array, print it directly
        echo "$key: $value<br>\n";
    }
}
?>