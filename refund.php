<?php    
    include_once 'credentials.php';
    include_once 'getRespose.php';
    if(isset($_GET['action']))     {
     // Insert order data into database
        
            $order_id = $order_data['id'];
            $id=$order_data['id'];
            $created_at = str_replace("T", " ", $order_data['created_at']);;
            $currency =$order_data['transactions'][0]['currency'] ?? 'N/A';
            $TotalAmount = $order_data['transactions'][0]['amount'] ?? 'N/A';
            $message= $order_data['transactions'][0]['message'] ?? 'N/A';
            $gateway=$order_data['transactions'][0]['gateway'] ?? 'N/A';
            $orderNumber  = $order_data['transactions'][0]['payment_id'] ?? 'N/A';    
            $orderNumber = str_replace('#', '',$orderNumber);
            $parts = explode('.', (string)$orderNumber); 
            if (isset($parts[1])) {
                        $fractionalValue = $parts[1]-1;
                        $orderNumberExt=(int)$orderNumber."R".$fractionalValue;     
                    }
            $orderNumber =(int)$orderNumber;

            echo  "Order number: ".$orderNumber;
            $orderDate = date('Y-m-d H:i:s', strtotime($order_data['processed_at']))?? '';
            $comments = $order_data['transactions'][0]['message'] ?? 'N/A';
            $creationDateTime = date('Y-m-d H:i:s')?? '';
            $OrderHeaderId= time();
            if (isset($order_data['return']['id'])) {
                //$TransactionType=6;
            } else {
                //$TransactionType=2;
            }
           
            // Database connection setup  
           
            $conn = new mysqli($servername, $username, $password, $database);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 
            
            $dateCalled = date('Y-m-d H:i:s'); // Current datetime
            $idhook = $order_id; // Generate a unique ID for the record
            $module = 'refund'; // Module name
            // Prepare the SQL statement
            $checkSql = "SELECT id FROM hooklog WHERE id = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("s", $idhook);
            $checkStmt->execute();
            $checkStmt->store_result();
            if ($checkStmt->num_rows == 0) 
                {
                    $sql = "INSERT INTO hooklog (id, module, datecalled) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sss", $idhook, $module, $dateCalled);
                    if ($stmt->execute()) {
                        echo "Record inserted successfully.";
                    } 
                    else 
                    {
                        echo "Error: " . $stmt->error;
                    }
                    $stmt->close();
                  
                        //refund_line_items is not empty.                
                        
                        //***************Enter receipt refund**************************************** */
                        //Sales return Refund with Item lines. 
                        //this code snippet is for order update page
                        if ( isset($order_data['returns']) && isset($order_data['refunds']) && !empty($order_data['return_line_items']) &&  !empty($order_data['refund_line_items'])  )
                       {
                            $TransactionType = 6; //this is not moneytory transaction
                       }
                       if ( isset($order_data['returns']) && isset($order_data['refunds']) && empty($order_data['return_line_items']) && !empty($order_data['refund_line_items'])  )
                       {
                            //$TransactionType = 4;
                       }
                       if (isset($order_data['returns']) && isset($order_data['refunds']) &&  !empty($order_data['return_line_items']) && empty($order_data['refund_line_items']) )
                       {
                            //$TransactionType = 6; NO money involved
                       }
                       if  (isset($order_data['returns']) && isset($order_data['refunds']) && empty($order_data['return_line_items']) && empty($order_data['refund_line_items']) )
                       {
                            $TransactionType = 3;
                       }
                         
                        $TotalAmount=-1*$TotalAmount;
                        $sql = "
                        INSERT INTO Receipt (id,order_id,order_id_ext,currency, payment_id,created_at, gateway, message,Type,amount,ReceiptDownloaded)
                        VALUES ('$id','$orderNumber','$orderNumberExt', '$currency', '$order_id', '$created_at', '$gateway', '$message', '$TransactionType', '$TotalAmount',0)
                        ";
                        $stmt = $conn->query($sql);
                        if (!$stmt) {
                            die('Prepare failed: ' . $conn->error);
                        } 

                        $sql = "UPDATE Receipt R, orderheaders O SET R.order_id = O.OrderNumber  WHERE O.OrderHeaderId = R.id and LENGTH(O.OrderHeaderId)>20";
                        if ($conn->query($sql) === TRUE) {
                            echo "Records updated successfully.";
                        } 
                        else
                        {
                        echo "Error updating records: " . $conn->error;
                        }  
                        //$stmt->close();
                        //*************************************** */ Insert line refund items data into database****************************************** 
                        $LineNumber=1;
                        $Ttotal_tax=0;
                        $total_tax=0;

                        //refund does not reduce stock amount. so next code are not needed
                    // foreach ($order_data['refund_line_items'] as $line_item) 
                    // {
                    //     $line_item_id =  $line_item['line_item']['product_id'] ?? 'N/A';
                    //     $current_quantity = $line_item['quantity'];
                    //     $tax_lines=$line_item['total_tax'];
                    //     $Ttotal_tax=$Ttotal_tax + $tax_lines;
                    //     $name = $line_item['line_item']['title'];                        
                    //     $price = $line_item['line_item']['price'];
                    //     $total_discount = $line_item['line_item']['total_discount'];
                    //     //$variant_id = $line_item['variant_id'];
                    //     $vendor = $line_item['line_item']['vendor'];
                    //     $sku=$line_item['line_item']['SKU'] ?? 'N/A';                        
                    //     $uniqueID=uniqid();
                    //     $sql = "INSERT INTO orderdetails (uniqueID,
                    //     orderheaderID,TransactionType,orderdetailsID,ordernumber,productid,Description,stockcode,UnitNettPrice, Quantitysold,fulfilledqty,tax_lines)
                    //     VALUES ('$uniqueID','$OrderHeaderId','$TransactionType','$LineNumber','$orderNumber','$line_item_id' , '$name', '$sku','$price','$current_quantity','0','$tax_lines')";
                    //     $LineNumber=$LineNumber+1;
                    //     if ($conn->query($sql) === TRUE) {
                    //         echo "Line item record inserted successfully.<br>";
                    //     } else {
                    //         echo "Error inserting line item record(orderdetails): " . $conn->error . "<br>";
                    //         $logFile = __DIR__ . '/shopify_errors.log';  // Log file in the same directory as the script                
                    //         $currentDateTime = date('Y-m-d H:i:s');      // Get current date and time
                    //         $errorMessage = "[$currentDateTime] Error: ". $conn->connect_error ;            
                    //         // Write the error message to the log file
                    //         file_put_contents($logFile, $errorMessage, FILE_APPEND);               
                    //         die('Error inserting line item record: ' . $conn->error);
                    //     }
                    //     }
                    //     // inserting header information of refund for line item refund. I am doing it after inserting line item because I need to sum tax from the total
                    //     $sql = "
                    //     INSERT INTO orderheaders (OrderHeaderId,OrderNumber, OrderDate, CurrencyCode, OrderGrossTotal, 
                    //     Comments, CreationDateTime,lastModifiedDateTime, transactionType,orderamount,total_tax
                    //     ) VALUES ('$OrderHeaderId','$orderNumber',  '$orderDate', '$currency', 
                    //     '$TotalAmount',  '$comments','$creationDateTime', '$creationDateTime',
                    //     '$TransactionType', '$TotalAmount','$Ttotal_tax')";
                    //     // Prepare the statement
                    //     $stmt = $conn->query($sql);
                    //     if (!$stmt) {
                    //         die('Prepare failed: ' . $conn->error);
                    //     }
                    // }  
                        $conn->close();   
          }
     else
    {
         echo 'Same data exists in hook log.';
     }
    }
         //*************************************************Refund ************************************ */
     ?>