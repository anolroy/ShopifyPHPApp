<?php    
    include_once 'credentials.php';
    include_once 'getRespose.php';
    
    $switch = 0; // 0 means SQL MODE and 1 means JSON MODE

    if (isset($_GET['action']))
   {        
    // Insert order data into database
        $order_id = $order_data['id'];
        $idhook = $order_data['id'];
        $dateCalled = date('Y-m-d H:i:s'); 
        $created_at = $order_data['created_at'];
        $currency = $order_data['currency'];
        $current_subtotal_price = $order_data['current_subtotal_price'];
        $OrderDiscount= $order_data['current_total_discounts'];
        $shippingCharge= $order_data['total_shipping_price_set']['shop_money']['amount'];
        $city ='';
        $zip ='';
        $country ='';
        $address1 ='';
        $address2 ='';
        // if (isset($orderData['shipping_address'])) {
        //     $shippingAddress = $orderData['shipping_address'];
        //     $address1 = $shippingAddress['address1'] ?? '';
        //     $address2 = $shippingAddress['address2'] ?? '';
        //     $city = $shippingAddress['city'] ?? '';
        //     $zip = $shippingAddress['zip'] ?? '';
        //     $country = $shippingAddress['country'] ?? '';
            
        //     // Now you can use the shipping address as needed
        // }
        $deliveryDateEarliest = null;
        $sourcePHP="order.php - ".$filename;
        // Check if the shipping_lines array exists
        if (isset($orderData['shipping_lines']) && is_array($orderData['shipping_lines'])) {
            foreach ($orderData['shipping_lines'] as $shippingLine) {
                // Check for the DeliveryDateEarliest field
                if (isset($shippingLine['delivery_date_earliest'])) {
                    $deliveryDateEarliest = $shippingLine['delivery_date_earliest'];
                    break; // Stop after finding the first occurrence
                }
            }
           }

            

            // Extract relevant data from the JSON response
            $order = $order_data;
           

            $orderNumber = $order['order_number']?? '';
            $orderDate = date('Y-m-d H:i:s', strtotime($order['created_at']))?? '';
            //$invoiceName = ($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '');
            //$invoiceAddress1 = "Address Placeholder"?? ''; // No address in the JSON, so placeholder
            $customer = $order['customer'];
            $shipping_address = $order['shipping_address'];
            $DeliveryAddress5=null;
            if (!empty($shipping_address)){
                $DeliveryName =$shipping_address['name']?? ''; // No address in the JSON, so placeholder
                $DeliveryAddress1 =$shipping_address['address1']?? '';
                $DeliveryAddress2=$shipping_address['address1']?? '';
                $DeliveryAddress3 =$shipping_address['address2']?? '';
                $DeliveryAddress4=$shipping_address['city'].'  '.$shipping_address['province'].'  '.$shipping_address['country']?? '';
                $DeliveryPostcode = $shipping_address['zip']?? '';
                $DeliveryTelephone_number =$shipping_address['phone']?? '';
            }            
            // $invaddressp1=$customer['default_address'];
            // if (!empty($invaddressp1)){
            //         $invoiceAddress1=$invaddressp1['first_name']?? ''.'  '.$invaddressp1['last_name']?? ''; // No address in the JSON, so placeholder
            //          $invoiceAddress2=$invaddressp1['company']?? '';
            //          $invoiceAddress3 =$invaddressp1['address1']?? '';
            //          $invoiceAddress4 =$invaddressp1['address2']?? '';
            //          $invoicePostcode=$invaddressp1['zip']?? '';
            //         //$DeliveryAddress5=$invaddressp1['city'].'  '.$invaddressp1['province'].'  '.$invaddressp1['country']?? '';
            // }
            $invaddressp1=$order_data['billing_address'];
            if (!empty($invaddressp1)){
                    $InvoiceName=$invaddressp1['first_name']?? ''.'  '.$invaddressp1['last_name']?? ''; // No address in the JSON, so placeholder
                     $invoicecompany=$invaddressp1['company']?? '';
                     $company=$invaddressp1['company']?? '';
                     $invoiceAddress1 =$invaddressp1['address1']?? '';//$invoiceAddress1
                     $invoiceAddress2 =$invaddressp1['address2']?? '';
                     $invoicePostcode=$invaddressp1['zip']?? '';
                     $invoiceAddress3=$invaddressp1['city'].'  '.$invaddressp1['province'].'  '.$invaddressp1['country']?? '';
            }

           
            $emailAddress = $customer['email'];
            $locationCode = $order['location_id']?? '';
          
            $total_tax = $order['total_tax']?? '';
            $currencyCode = $order['currency']?? '';
           // $comments = "Generated from Shopify API"?? '';
            $comments =  $order['note'];
            $creationDateTime = date('Y-m-d H:i:s')?? '';
            $paymentMethod = null;//$order['financial_status']?? ''; // Mapping financial status as payment method
            $fulfillment_status = $order['fulfillment_status']?? '';
            $financial_status = $order['financial_status']?? '';
            $orderamount=$order['current_total_price'];
            $orderGrossTotal = $order['total_price']?? '';
            
            $updated_at=$order['updated_at'];
            $OrderHeaderId= $order_data['id']; 
            $discount_code=null;
            $discount_amount=null;
            $discount_type=null;
            $discount_target_type=null;
            $discount_value=null;
            $discount_allocation_method=null;
            $discount_target_selection=null;
        //**** disocunt header */
                if (!empty($order['discount_codes'])) {
                    foreach ($order['discount_codes'] as $allocation) {
                       
                        $discount_code = $allocation['code'];
                        $discount_amount =  $allocation['amount'];
                        $discount_type = $allocation['type'];
                        
                        // You can decide how to handle multiple discounts (sum them, use the largest, etc.)
                    }
                }

                foreach ($order_data['line_items'] as $line_item) {
                    if (!empty($line_item['discount_allocations'])) {
                        foreach ($line_item['discount_allocations'] as $allocation) {                           
                                    $index = $allocation['discount_application_index'];                    
                                    // Fetch the corresponding discount application
                              $discount_application = $order_data['discount_applications'][$index]; 
                              if ($discount_application['target_selection']=='all'){                      
                                            // Extract the relevant discount details
                                            $discount_target_type = $discount_application['target_type'];
                                        // $discount_type =  $discount_application['type']; this shall not come
                                            $discount_value = $discount_application['value'];// this shall be inserted in value field
                                           // $discount_type = $discount_application['value_type']; no need
                                            $discount_allocation_method = $discount_application['allocation_method'];
                                            $discount_target_selection =$discount_application['target_selection'];                                            
                                    }
                            // You can decide how to handle multiple discounts (sum them, use the largest, etc.)
                        }
                    }
                }
      //end  discount header
                $shipping_tax=0;
               //geting shipping header
               $conn = new mysqli($servername, $username, $password, $database);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 


            $module = 'Sales Order'; // Module name
            // Prepare the SQL statement
            // we do not need Order Update in 'Order Update' because this document modifies several time and allowed to come multiple times
            //1030-R1 in one orderupdate 1030 -R2 in orderupdate. where theier Id is same. so we cannot check duplicate on document id
        
            $sql = "INSERT INTO hooklog (id, module, datecalled) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $idhook, $module, $dateCalled);
            if ($stmt->execute()) {
                echo "Record inserted successfully..<br>";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();

               if (isset($order_data['shipping_lines'])) 
               {
                    foreach ($order_data['shipping_lines'] as $shippingLine) 
                    {
                        $ChargeAmount=$shippingLine['price'];
                        $Description=$shippingLine['title'];
                        $TransactionType=1;
                        $ShippingChargeID=$shippingLine['id'];
                        $sql = "Delete from  Shippingdetails where ShippingChargeID='$ShippingChargeID'";
                        $result = $conn->query($sql); 
                        if (isset($shippingLine['tax_lines'])) 
                        {
                            foreach ($shippingLine['tax_lines'] as $taxLine) 
                            {
                                if (isset($taxLine['price'])) {
                                    //echo "Tax Price: " . $taxLine['price'] . "\n";
                                        $shipping_tax=$shipping_tax+ $taxLine['price'];                                                                        
                                        if ($switch == 0) {
                                            $sql = "INSERT INTO Shippingdetails (ShippingChargeID ,OrderHeaderId , OrderNumber ,TransactionType ,Description, ChargeAmount, TaxAmount)
                                            VALUES ('$ShippingChargeID' ,'$OrderHeaderId' ,'$orderNumber' ,'$TransactionType' ,'$Description', '$ChargeAmount', '$shipping_tax')";
                                            $result = $conn->query($sql);
                                        } else {
                                            $shippingData = array(
                                                'ShippingChargeID' => $ShippingChargeID,
                                                'OrderHeaderId' => $OrderHeaderId,
                                                'OrderNumber' => $orderNumber,
                                                'TransactionType' => $TransactionType,
                                                'Description' => $Description,
                                                'ChargeAmount' => $ChargeAmount,
                                                'TaxAmount' => $shipping_tax
                                            );

                                            // Read existing content if file exists
                                            $existingContent = [];
                                            if (file_exists('1shippingDetails.json')) {
                                                $existingContent = json_decode(file_get_contents('1shippingDetails.json'), true);
                                            }

                                            // Add new shipping data
                                            $existingContent[] = $shippingData;

                                            // Write back to file
                                            file_put_contents('1shippingDetails.json', json_encode($existingContent, JSON_PRETTY_PRINT));
                                        }
                                }
                            }
                        }
                    }
                }            
            // Database connection setup    
           
                        
            $dateCalled = date('Y-m-d H:i:s'); // Current datetime
            $idhook = $order_id; // Generate a unique ID for the record
            $module = 'order'; // Module name
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

            $checkSql = "SELECT OrderHeaderId FROM orderheaders WHERE OrderNumber = ? and transactionType=1";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param('i', $orderNumber);
            $stmt->execute();
            $stmt->store_result();
            
            
            // If order exists, skip insertion // 0 means SQL MODE and 1 means JSON MODE
            if ($switch == 0 && $stmt->num_rows > 0) {
                echo "Duplicate order received. Skipping insertion for Order ID: " . $orderNumber;
                $stmt->close();
            } else {
                // $sql = "UPDATE Receipt R, orderheaders O SET R.order_id = O.OrderNumber  WHERE O.OrderHeaderId = R.id and LENGTH(O.OrderHeaderId)>20 AND ((R.order_id)='' or isnull(R.order_id))";
                // if ($conn->query($sql) === TRUE) {
                //     echo "Records updated successfully.";
                // } 
                // else
                // {
                // echo "Error updating records: " . $conn->error;
                // }      
            $transactionType=1;
            $transactionTypeDesc="Sales Order";   
            $orderGrossTotal= $orderamount+ $shippingCharge+ $shipping_tax;    
            if ($switch == 0) {
                $sql = "INSERT INTO orderheaders (
                    OrderHeaderId, OrderNumber, OrderDate, DeliveryDateEarliest,TransactionType,transactionTypeDesc,InvoiceName, company,DeliveryName,InvoiceAddress1, EmailAddress, CurrencyCode, OrderGrossTotal, Comments,
                    CreationDateTime, lastModifiedDateTime, PaymentMethod, fulfilled,  financial_status,  orderamount, OrderDiscount,shippingCharge,
                    Code, Amount, Type, target_type, value, allocation_method, target_selection, total_tax,InvoiceAddress2,InvoiceAddress3, 
                    InvoiceAddress4, InvoicePostcode, DeliveryAddress1, DeliveryAddress2, DeliveryAddress3, DeliveryAddress4, DeliveryAddress5,DeliveryPostcode, DeliveryTelephone_number,shipping_tax,sourcePHP                   
                ) VALUES ('$OrderHeaderId', '$orderNumber', '$orderDate', '$deliveryDateEarliest', '$transactionType','$transactionTypeDesc','$InvoiceName',' $company','$DeliveryName', '$invoiceAddress1','$emailAddress', '$currencyCode', '$orderGrossTotal', '$comments', 
                    '$creationDateTime', '$updated_at', '$paymentMethod', '$fulfillment_status', '$financial_status', '$orderamount', '$OrderDiscount', '$shippingCharge', 
                    '$discount_code', '$discount_amount', '$discount_type','$discount_target_type', '$discount_value',' $discount_allocation_method', '$discount_target_selection','$total_tax','$invoiceAddress2','$invoiceAddress3', 
                    '$invoiceAddress4', '$invoicePostcode', '$DeliveryAddress1', '$DeliveryAddress2', '$DeliveryAddress3', '$DeliveryAddress4', '$DeliveryAddress5','$DeliveryPostcode','$DeliveryTelephone_number','$shipping_tax','$sourcePHP' )";
                $result = $conn->query($sql);
                $stmt->close();
            } else {
                $orderHeaderData = array(
                    'OrderHeaderId' => $OrderHeaderId,
                    'OrderNumber' => $orderNumber,
                    'OrderDate' => $orderDate,
                    'DeliveryDateEarliest' => $deliveryDateEarliest,
                    'TransactionType' => $transactionType,
                    'transactionTypeDesc' => $transactionTypeDesc,
                    'InvoiceName' => $InvoiceName,
                    'company' => $company,
                    'DeliveryName' => $DeliveryName,
                    'InvoiceAddress1' => $invoiceAddress1,
                    'EmailAddress' => $emailAddress,
                    'CurrencyCode' => $currencyCode,
                    'OrderGrossTotal' => $orderGrossTotal,
                    'Comments' => $comments,
                    'CreationDateTime' => $creationDateTime,
                    'lastModifiedDateTime' => $updated_at,
                    'PaymentMethod' => $paymentMethod,
                    'fulfilled' => $fulfillment_status,
                    'financial_status' => $financial_status,
                    'orderamount' => $orderamount,
                    'OrderDiscount' => $OrderDiscount,
                    'shippingCharge' => $shippingCharge,
                    'Code' => $discount_code,
                    'Amount' => $discount_amount,
                    'Type' => $discount_type,
                    'target_type' => $discount_target_type,
                    'value' => $discount_value,
                    'allocation_method' => $discount_allocation_method,
                    'target_selection' => $discount_target_selection,
                    'total_tax' => $total_tax,
                    'InvoiceAddress2' => $invoiceAddress2,
                    'InvoiceAddress3' => $invoiceAddress3,
                    'InvoiceAddress4' => $invoiceAddress4,
                    'InvoicePostcode' => $invoicePostcode,
                    'DeliveryAddress1' => $DeliveryAddress1,
                    'DeliveryAddress2' => $DeliveryAddress2,
                    'DeliveryAddress3' => $DeliveryAddress3,
                    'DeliveryAddress4' => $DeliveryAddress4,
                    'DeliveryAddress5' => $DeliveryAddress5,
                    'DeliveryPostcode' => $DeliveryPostcode,
                    'DeliveryTelephone_number' => $DeliveryTelephone_number,
                    'shipping_tax' => $shipping_tax,
                    'sourcePHP' => $sourcePHP
                );

                // Read existing content if file exists
                $existingContent = [];
                if (file_exists('1Orderheader.json')) {
                    $existingContent = json_decode(file_get_contents('1Orderheader.json'), true);
                }

                // Add new order header data
                $existingContent[] = $orderHeaderData;

                // Write back to file
                file_put_contents('1Orderheader.json', json_encode($existingContent, JSON_PRETTY_PRINT));
            }
            $index = null;                                
            $discount_application = null;                              
            $discount_target_type = null;
            $discount_type = null;
            $discount_value = null;;
            $discount_value_type = null;;
            $discount_allocation_method =null;;
            $discount_target_selection =null;;
        //*************************************** */ Insert line items data into database******************************************
        $LineNumber=1;
        foreach ($order_data['line_items'] as $line_item) {
            $line_item_id = $line_item['product_id'];
            $current_quantity = $line_item['current_quantity'];
            $name = $line_item['name'];
            $price = $line_item['price'];
            $total_discount = $line_item['total_discount'];
            $variant_id = $line_item['variant_id'];
            $vendor = $line_item['vendor'];
            $sku=$order_data['line_items'][$LineNumber-1]['sku'];
            $taxable=($line_item['taxable'] == 1) ? 1 : 0;;
            
            $discount_target_type = null;
            $discount_type = null;
            $discount_value = null;
            $discount_value_type = null;
            $discount_allocation_method = null;
            $discount_target_selection = null; 
            $tax_line_price=0;
            foreach ($line_item['tax_lines'] as $tax_lines) { 
                if (isset($tax_lines['price']) ) {                    
                        $tax_line_price= $tax_lines['price'];
                    
                } 
            }

            // Calculate Unit Tax Value
            $unitTaxValue = $tax_line_price / $current_quantity;

            // Check if discounts exist for this line item
            if (!empty($line_item['discount_allocations'])) {
                foreach ($line_item['discount_allocations'] as $allocation) {
                    
                            $index = $allocation['discount_application_index'];                    
                            // Fetch the corresponding discount application
                            $discount_application = $order_data['discount_applications'][$index];                    
                            // Extract the relevant discount details
                    if ($discount_application['target_selection']!=='all'){   
                            $discount_target_type = $discount_application['target_type'];
                            $discount_type =  $discount_application['type'];
                            $discount_value = $discount_application['value'];
                            $discount_value_type = $discount_application['value_type'];
                            $discount_allocation_method = $discount_application['allocation_method'];
                            $discount_target_selection =$discount_application['target_selection'];
                            echo $discount_target_type;
                            echo $discount_type;
                    }
                    // You can decide how to handle multiple discounts (sum them, use the largest, etc.)
                }
            }
            $uniqueID=$line_item['id'];
            $UnitPriceAfterDiscount=$price-$total_discount;
            if ($switch == 0) {
                $sql = "INSERT INTO orderdetails (
                    uniqueID, orderheaderID, orderdetailsID, ordernumber, TransactionType, transactionTypeDesc, productid, Description, stockcode, UnitNettPrice, Quantitysold, FulfilledQty, taxable, target_type, type, value,
                    value_type, allocation_method, target_selection, LineDiscountValue, tax_lines, UnitPriceAfterDiscount, UnitTaxValue
                ) VALUES (
                    '$uniqueID', '$OrderHeaderId', '$LineNumber', '$orderNumber', '$transactionType', '$transactionTypeDesc', '$line_item_id', '$name', '$sku', '$price', '$current_quantity', '0', '$taxable',
                    '$discount_target_type', '$discount_type', '$discount_value', '$discount_value_type', '$discount_allocation_method', '$discount_target_selection', '$total_discount', '$tax_line_price',
                    '$UnitPriceAfterDiscount', '$unitTaxValue'
                )";
               
                if ($conn->query($sql) === TRUE) {
                    echo "Line item record inserted successfully.<br>";
                } else {
                    echo "Error inserting line item record: " . $conn->error . "<br>";
                    $logFile = __DIR__ . '/shopify_errors.log';  // Log file in the same directory as the script                
                    $currentDateTime = date('Y-m-d H:i:s');      // Get current date and time
                    $errorMessage = "[$currentDateTime] Error: ". $conn->connect_error ;            
                    // Write the error message to the log file
                    file_put_contents($logFile, $errorMessage, FILE_APPEND);               
                    die('Error inserting line item record: ' . $conn->error);
                }
            } else {
                $orderDetails = array(
                    'uniqueID' => $uniqueID,
                    'orderheaderID' => $OrderHeaderId,
                    'orderdetailsID' => $LineNumber,
                    'ordernumber' => $orderNumber,
                    'TransactionType' => $transactionType,
                    'transactionTypeDesc' => $transactionTypeDesc,
                    'productid' => $line_item_id,
                    'Description' => $name,
                    'stockcode' => $sku,
                    'UnitNettPrice' => $price,
                    'Quantitysold' => $current_quantity,
                    'FulfilledQty' => 0,
                    'taxable' => $taxable,
                    'target_type' => $discount_target_type,
                    'type' => $discount_type,
                    'value' => $discount_value,
                    'value_type' => $discount_value_type,
                    'allocation_method' => $discount_allocation_method,
                    'target_selection' => $discount_target_selection,
                    'LineDiscountValue' => $total_discount,
                    'tax_lines' => $tax_line_price,
                    'UnitPriceAfterDiscount' => $UnitPriceAfterDiscount,
                    'UnitTaxValue' => $unitTaxValue
                );

                // Read existing content if file exists
                $existingContent = [];
                if (file_exists('1orderdetails.json')) {
                    $existingContent = json_decode(file_get_contents('1Order.json'), true);
                }

                // Add new order details
                $existingContent[] = $orderDetails;

                // Write back to file
                file_put_contents('1orderdetails.json', json_encode($existingContent, JSON_PRETTY_PRINT));
            }
            $LineNumber=$LineNumber+1;
        }     
        }
    }
    //*************************************************end sales order ************************************ */
    ?>