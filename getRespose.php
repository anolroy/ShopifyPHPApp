<?php

// Define the directory for archiving files
$archiveDir = __DIR__ . '/archive';


// Function to sanitize the 'action' parameter
function sanitize_action($action) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', $action);
}

// Ensure the archive directory exists
if (!is_dir($archiveDir)) {
    mkdir($archiveDir, 0755, true);
}

if (isset($_GET['action'])) {
    // Sanitize the 'action' parameter
    $action = sanitize_action($_GET['action']);
    
    if (!isLocalServer) {
        $hmac_header = isset($_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256']) ? $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'] : null;

        /**
         * Verify the Shopify webhook
         *
         * @param string $data The raw POST data
         * @param string $hmac_header The HMAC header from Shopify
         * @return bool True if verification succeeds, false otherwise
         */
        function verify_webhook($data, $hmac_header) {
            $calculated_hmac = base64_encode(hash_hmac('sha256', $data, SHOPIFY_APP_SECRET, true));
            return hash_equals($hmac_header, $calculated_hmac);
        }

        if ($hmac_header === null) {
            // Handle case where header is missing
            $response = 'HTTP_X_SHOPIFY_HMAC_SHA256 header is missing.';
        } else {
            // Retrieve the raw POST data
            $data = file_get_contents('php://input');
            $verified = verify_webhook($data, $hmac_header);

            if ($verified) {
                $response = $data;
                // If you need to process the order ID, uncomment and modify the following line
                // $order_id = json_decode($response, true)['order']['id'];
            } else {
                $response = 'Verification failed';
            }
        }
        $order_data = json_decode($response, true);
        if ($action =='order')
            {
                $orderNumber = $order_data['order_number']?? ''  ;  
            }
        else if ($action =='fulfillment')
            {
                $orderId = str_replace('#', '',$order_data['name']);        
                $orderNumber =(int)$orderId;  
        
            }
        else if ($action =='refund')
            {
                $orderNumber = $order_data['transactions'][0]['payment_id'] ?? 'N/A';    
                $orderNumber = str_replace('#', '',$orderNumber);       
                   
            }
        else if ($action =='receipt')
            {
                $orderNumber = str_replace('#', '',$order_data['payment_id']); 
            }
        else if ($action =='orderupdate')
            {    
                $orderNumber = str_replace('#', '',$order_data['order_number']);
            }    
        // Generate a timestamp
        $timestamp = date('Ymd_His'); // Format: YYYYMMDD_HHMMSS
        // Construct the filename with action and timestamp
        $filename = "{$archiveDir}/{$timestamp}_{$orderNumber}_{$action}.json";
        // Save the response to the file
        $log = fopen($filename, 'w') or die('Cannot open the file for writing.');
        fwrite($log, $response);
        fclose($log);

        $log = fopen($_GET['action'].'.json', 'w') or die('cant open the file');
        fwrite($log, $response);
        fclose($log);        
        // Decode the JSON response
        
    } else 
    {
        // ************ Handling for Local Server ************

        $jsonContent = file_get_contents($_GET['action'].'.json');
                    if ($jsonContent === false) {
                       // die('Error: Could not open orders.json');
                    }
                    // Decode the JSON string into a PHP associative array
                    $response = json_decode($jsonContent, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                       // die('Error: Invalid JSON - ' . json_last_error_msg());
                    }           
                    $order_data=$response;            
                    //***************END ******************** */
                    
                    if ($order_data === null) 
                        {
                            echo "Order data  null";
                           // die('Error decoding JSON file.');
                            $log = fopen('shopify_errors.json', 'w') or die('cant open the file');
                            fwrite($log, 'order data is null');
                            fclose($log);            
                        }
                    else
                        echo "Order data not null";
    }

    // Optional: Further processing with $order_data
    // Example:
    // if ($order_data) {
    //     // Process the order data
    // }
} else {
    echo "No action specified.";
}
?>
