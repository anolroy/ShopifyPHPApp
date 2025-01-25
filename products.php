<?php
use GuzzleHttp\Client; 
include_once 'credentials.php';

function fetchShopifyProducts($shopifyStoreUrl, $shopifyApiPassword) {
    echo "fetchShopifyProducts function called";
    $client = new Client([
        'base_uri' => "https://$shopifyStoreUrl/admin/api/2023-10/graphql.json",
    ]);
   
    $query = <<<'GQL'
    {
        products(first: 250) {
            edges {
                node {
                    id
                    title
                    descriptionHtml
                    variants(first: 15) {
                        edges {
                            node {
                                id
                                price
                                sku
                                taxable                                   
                            }
                        }
                    }
                }
            }
        }
    }
    GQL;

    $response = $client->request('POST', '', [
        'headers' => [
            'X-Shopify-Access-Token' => $shopifyApiPassword,
            'Content-Type' => 'application/json',
        ],
        'json' => ['query' => $query],
    ]);
    $responseBody = $response->getBody()->getContents();    
    $log = fopen('stockRecords.json', 'w') or die('Cannot open the file');
    fwrite($log, $responseBody);
    fclose($log); 

    $body = json_decode($response->getBody(), true);
    return $body['data']['products']['edges'] ?? [];
}

if (isset($_GET['action'])) { 
    try {
       // echo $shopifyStoreUrl;
        //echo $shopifyApiPassword;
        $products = fetchShopifyProducts($shopifyStoreUrl, $shopifyApiPassword);
        $productLog = fopen('1Product.json', 'w') or die('Cannot open the file');
        fwrite($productLog, json_encode($products, JSON_PRETTY_PRINT));
        fclose($productLog);
        // Database connection
        $conn = new mysqli($servername, $username, $password, $database);
        if ($conn->connect_error) {
            $logFile = __DIR__ . '/shopify_errors.log';  // Log file in the same directory as the script
            $currentDateTime = date('Y-m-d H:i:s');      // Get current date and time
            $errorMessage = "[$currentDateTime] Error: ". $conn->connect_error ;
        
            // Write the error message to the log file
            file_put_contents($logFile, $errorMessage, FILE_APPEND);
        
            // Optionally print the error to the screen
            echo 'Error1: '. $conn->connect_error;
            die('Connection failed: ' . $conn->connect_error);
        }
        $id = uniqid(); // Generate a unique ID for the record
        $module = 'Product'; // Module name
        $sql = "SELECT MAX(datecalled) AS last_called FROM hooklog WHERE module = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $module);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row && $row['last_called']) {
            $lastCalled = strtotime($row['last_called']); // Convert to timestamp
            $currentTime = time(); // Current timestamp

            if (($currentTime - $lastCalled) <= 30) {
                echo "The module 'Product' was called recently. Skipping 'createfruite' function.";
                $stmt->close();
                $conn->close();
                exit; // Exit script to avoid calling the function
            }
        }

        $dateCalled = date('Y-m-d H:i:s'); // Current datetime

        // Prepare the SQL statement
        $conn->begin_transaction();
        $sql = "INSERT INTO hooklog (id, module, datecalled) VALUES (?, ?, ?)";

        // Prepare and bind
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $id, $module, $dateCalled);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Record inserted successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        // Close the statement and connection
        $stmt->close();
        
        $processedProductIDs = []; 
        $productData = [];
        // First, get all existing variants from the database
        $existingVariants = [];
        $getExistingSQL = "SELECT variant_id, StockCode FROM stockrecords";
        $result = $conn->query($getExistingSQL);
        while ($row = $result->fetch_assoc()) {
            $existingVariants[$row['variant_id']] = $row['StockCode']; // Store current SKU for each variant
        }

        foreach ($products as $productEdge) {
            $product = $productEdge['node'];
            $productID = str_replace('gid://shopify/Product/', '', $product['id']);
            $title = $product['title'];

            if (!empty($product['variants']['edges'])) {
                foreach ($product['variants']['edges'] as $variantEdge) {
                    $variant = $variantEdge['node'];
                    $variantID = str_replace('gid://shopify/ProductVariant/', '', $variant['id']);
                    // Set default SKU if null or empty
                    //$sku = !empty($variant['sku']) ? $variant['sku'] : 'SKU-' . $variantID;
                    $sku = !empty($variant['sku']) ? $variant['sku'] : '';
                    $price = $variant['price'];
                    $taxable = $variant['taxable'];
                    $currentDateTime = date('Y-m-d H:i:s');

                    // Check if variant exists and if SKU has changed
                    if (array_key_exists($variantID, $existingVariants)) {
                        // Only update if SKU is not null and has changed
                        if (!empty($sku) && $existingVariants[$variantID] !== $sku) {
                            $updateSQL = "UPDATE stockrecords SET 
                                StockCode = ?,
                                Description = ?,
                                taxable = ?,
                                SellPrice = ?
                                WHERE variant_id = ?";
                            
                            $updateStmt = $conn->prepare($updateSQL);
                            $updateStmt->bind_param(
                                'sssss',
                                $sku,
                                $title,
                                $taxable,
                                $price,
                                $variantID
                            );
                            
                            if ($updateStmt->execute()) {
                                echo "Updated variant {$variantID} with new SKU: {$sku} (old SKU: {$existingVariants[$variantID]})\n";
                            } else {
                                echo "Error updating variant: " . $updateStmt->error . "\n";
                            }
                            $updateStmt->close();
                        }
                    } else {
                        // Add new product/variant to productData array for insertion
                        $productData[] = [
                            'stockID' => time() . rand(1000, 9999),
                            'sku' => $sku,
                            'productID' => $productID,
                            'title' => $title,
                            'taxable' => $taxable,
                            'price' => $price,
                            'variantID' => $variantID,
                            'creationDateTime' => $currentDateTime,
                        ];
                    }
                }
            }
        }

        // Insert only new products
        if (!empty($productData)) {
            $sql = "INSERT INTO stockrecords (
                    stockID, StockCode, ProductID, Description, taxable, SellPrice, 
                    variant_id, CreationDateTime
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            foreach ($productData as $data) {
                $stmt->bind_param(
                    'isssssss',
                    $data['stockID'], $data['sku'], $data['productID'], $data['title'], 
                    $data['taxable'], $data['price'], $data['variantID'], $data['creationDateTime']
                );

                if ($stmt->execute()) {
                    echo "Inserted new: StockID {$data['stockID']} - {$data['title']}\n";
                } else {
                    echo "Error: " . $stmt->error . "\n";
                }
            }
            $stmt->close();
        }
       
       
        $conn->commit(); 
        $conn->close();
    }
    catch (Exception $e) {
        $logFile = __DIR__ . '/shopify_errors.log'; 
        $currentDateTime = date('Y-m-d H:i:s'); 
        $errorMessage = "[$currentDateTime] Error: " . $e->getMessage() . PHP_EOL;
        $conn->rollback();
        file_put_contents($logFile, $errorMessage, FILE_APPEND);
    
        echo 'Error1: ' . $e->getMessage();
    }
}
?>
