<?php    
include_once 'credentials.php';
include_once 'getRespose.php';

if (isset($_GET['action'])) {
    if (isset($order_data['line_items'])) {
        $orderId = str_replace('#', '',$order_data['name']); 
        $fulfillmentNo=str_replace('.', '-F',$orderId);       
        $orderId =(int)$orderId;       
        $conn = new mysqli($servername, $username, $password, $database);
                                    if ($mysqli->connect_error) {
                                        die('Connection failed: ' . $mysqli->connect_error);
                                    }
            $id=$order_data['id'];

            $dateCalled = date('Y-m-d H:i:s'); // Current datetime
            $idhook =$id; // Generate a unique ID for the record
            $module = 'fulfillment'; // Module name
            // Prepare the SQL statement
            $sql = "INSERT INTO hooklog (id, module, datecalled) VALUES (?, ?, ?)";
            // Prepare and bind
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $idhook, $module, $dateCalled);

            // Execute the statement
            if ($stmt->execute()) {
                echo "Record inserted successfully.";
            } else {
                echo "Error: " . $stmt->error;
            }


            $checkSql = "SELECT ID FROM fulfillment WHERE ID = ?";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->store_result();

            // If order exists, skip insertion
            if ($stmt->num_rows > 0) {
                echo "Duplicate fulfillment received. Skipping insertion for receipt ID: ";
                $stmt->close();
                        } else { 
                                foreach ($order_data['line_items'] as $lineItem) {
                                    echo $orderId;
                                    $rowID= $lineItem['id'] ?? 'N/A';
                                    $productId = $lineItem['product_id'] ?? 'N/A';
                                    $quantity = $lineItem['quantity'] ?? 'N/A';
                                    $description = $lineItem['name'] ?? 'N/A';
                                    $sku= $lineItem['SKU'] ?? '';
                                   

                                    // Database connection setup            
                                    

                                    // Update statement
                                    $sql = "UPDATE orderdetails SET FulfilledQty = FulfilledQty + ? WHERE ordernumber = ? AND productid = ?";
                                    $stmt = $conn->prepare($sql);
                                    if (!$stmt) {
                                        die('Prepare failed: ' . $mysqli->error);
                                    }

                                    $stmt->bind_param(
                                        'iss', 
                                        $quantity, 
                                        $orderId,
                                        $productId
                                    );

                                    if ($stmt->execute()) {
                                        echo "Fulfillment updated successfully. Order ID 855: <br> " . $mysqli->insert_id;
                                    } else {
                                        echo "Error: " . $stmt->error;
                                    }

                                    // $stmt->close();
                                    echo "Hello b"."<br>";
                                    echo $order_data['name']."<br>" ;
                                    echo $description."<br>";
                                    echo $orderId."<br>";
                                    echo $productId."<br>";
                                    echo $quantity."<br>";

                                
                                    $sql = "INSERT INTO fulfillment (ID,rowID,InvoiceID,fulfillmentNo,product_id,sku,description, quantity,downloaded) VALUES (?,?,?,?,?,?, ?,?,0)";
                                    $stmt = $conn->prepare($sql);

                                    if (!$stmt) {
                                    
                                        die('Prepare failed: ' . $mysqli->error);                
                                    }
                                    else
                                    

                                    // Bind parameters for the insert statement
                                    $stmt->bind_param(
                                        'sssssssi',
                                        $id,               
                                        $rowID,
                                        $orderId,
                                        $fulfillmentNo,                                
                                        $productId,
                                        $sku, 
                                        $description,              
                                        $quantity
                                    );
                                    
                                    if ($stmt->execute()) {
                                        echo "Fulfillment table data inserted successfully. ID: " . $mysqli->insert_id;
                                    } else {
                                        echo "Error: " . $stmt->error;
                                    }

                                    $stmt->close();
                                }
                                $conn->close();
                        }
            }     
}
?>
