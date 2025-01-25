<?php    
        include_once 'credentials.php';
        include_once 'getRespose.php';
        if(!isset($_GET['action'])) 
        {
             die('action not set ');
        }
              // Insert order data into database
        $conn = new mysqli($servername, $username, $password, $database);
        if ($conn->connect_error) {
            die('Connection failed: ' . $mysqli->connect_error);
        } 
        //$orderId = $order_data['order_id'] ?? 'N/A';
        $document_id = $order_data['order_id'] ?? 'N/A';// as this is primary key. if it comes duplicate we shall use unique key later
        $amount = $order_data['amount'] ?? 'N/A';
        $currency = $order_data['currency'] ?? 'N/A';
        $paymentId = $order_data['payment_id']; 
        $orderNumber =str_replace('#', '',$order_data['payment_id']);
        // $parts = explode('.', (string) $orderNumber);
        // if (isset($parts[0])) {
        //     $order_id = $parts[0];                                  
        // }
       // $position = strpos($paymentId, '.');
        $dateCalled = date('Y-m-d H:i:s'); // Current datetime
        $idhook = $order_data['order_id'] ?? 'N/A';; // Generate a unique ID for the record
        $module = 'receipt'; // Module name 


        $orderId = $order_data['order_id'];
        // Build the API URL
        $orderApiUrl = $apiUrl . $orderId . ".json";
        // Make the API call to fetch the order details
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $orderApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$shopifyApiKey:$shopifyApiPassword");
        $response = curl_exec($ch);
        if ($response === false) {
            die('Error fetching order: ' . curl_error($ch));
        }
        curl_close($ch);
        // Decode the API response
        $orderDetails = json_decode($response, true);
        $taxAmount =0;
        // Extract tax amount
        $taxAmount =0;
        $order_number ="";
        if (isset($orderDetails['order']['total_tax'])) {
            $taxAmount = $orderDetails['order']['total_tax'];
            $order_number = $orderDetails['order']['order_number'];
            echo "The tax amount for order ID $orderId is: $taxAmount GBP";
        } else {
            echo "No tax information found for order ID $orderId.";
        }


        // $sql = "UPDATE Receipt R, orderheaders O SET R.order_id = O.OrderNumber WHERE O.OrderHeaderId = R.id and LENGTH(R.order_id)>20";
        // if ($conn->query($sql) === TRUE) {
        //      echo "Records updated successfully.";
        // } else {
        //         echo "Error updating records: " . $conn->error;
        // }       
        $sql = "INSERT INTO hooklog (id, module, datecalled) VALUES (?, ?, ?)";
        //if (strpos($paymentId, '.') !== false)
        //if (preg_match('/^\d+\.\d+$/', $paymentId)) 
        if (strpos($paymentId, '.') !== false)
        {
            $TransactionType=5;
            $TransactionTypeDesc="Payment due later";
            $parts = explode('.', (string) $orderNumber);
            if (isset($parts[0])) {
                $orderNumber = $parts[0];                                  
            }
        }
        else
        {
            $TransactionType=1;
            $TransactionTypeDesc="Sales order receipt";
            $orderNumber=$order_number;
        }
         $SourcePHP="receipt.php - ". $filename ;
         $SourceID=$order_data['payment_id'];
        // Prepare and bind
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $idhook, $module, $dateCalled);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Record inserted successfully.<br>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $checkSql = "SELECT order_id FROM Receipt WHERE id = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param('i', $document_id);
        $stmt->execute();
        $stmt->store_result();
        echo $document_id."<br>";
        // If order exists, skip insertion
        if ($stmt->num_rows > 0) {
                echo "Duplicate receipt received. Skipping !!: ";
            }
        else
        {            
            //check is any receipt order_id is very long then update that order_id from order table by joining  
            // Do this here and when order update is done.                
            $checkSql = "SELECT payment_id FROM Receipt WHERE payment_id = ?";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                echo "Duplicate receipt received.: ". $checkSql. $stmt->num_rows;
                $stmt->close();
            } 
                else 
            {   

                $gateway = $order_data['gateway'] ?? 'N/A';
                $message = $order_data['message'] ?? 'N/A';
                $createdAt = $order_data['created_at'] ?? 'N/A';
                // Remove the "T" character
                $createdAtFormatted = str_replace("T", " ", $createdAt);
                //echo $createdAtFormatted;
            
                $kind = $order_data['kind'] ?? 'N/A';
                $sql = "
                INSERT INTO Receipt (id,order_id, currency, payment_id,TransactionTypeDesc,SourcePHP,SourceID, gateway,created_at,message,TotalTax, amount, Type, ReceiptDownloaded )
                VALUES ('$document_id','$orderNumber', '$currency', '$paymentId', '$TransactionTypeDesc','$SourcePHP','$SourceID','$gateway', '$createdAtFormatted', '$message', 
                '$taxAmount','$amount','$TransactionType',0)
                ";
                
                
                // Execute the statement
                if ($conn->query($sql)) {
                    echo "Data inserted successfully.";
                } else {
                    echo "Error: " . $conn->error;
                }                   
                //$stmt->close();
                // Update query
                // $sql = "UPDATE Receipt R, orderheaders O 
                // SET R.order_id = O.OrderNumber 
                // WHERE O.OrderHeaderId = R.id and LENGTH(R.payment_id)>20;";

                // if ($conn->query($sql) === TRUE) {
                //     echo "Records updated successfully.";
                // } else {
                //     echo "Error updating records: " . $conn->error;
                // }
                $conn->close();
            }
        }    
    //***************************************************** */   receipt end***************************************
    ?>