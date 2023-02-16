<?php
$orderID =str_replace('-', '', date('Y-m-dH-m-s'));
        $clientId = "xxxx";// Input Clientid here
        $requestId = $orderID;
        $requestDate = date('Y-m-d\TH:i:s\Z');
        $targetPath = "/checkout/v1/payment"; // For merchant request to Jokul, use Jokul path here. For HTTP Notification, use merchant path here
        $secretKey = "xxx";// Input Client Secret here
        $requestBody = array(
            'order' =>
            array(
                'amount' => 100000,
                'invoice_number' => $orderID
            ),
            'payment' =>
            array(
                'payment_due_date' => 120
            ),
            'customer' =>
            array(
                'id' => 'customer',
                'name' => 'test it',
                'email' => 'itphi@example.com'
            )
        );
        
        // Generate Digest
        $digestValue = base64_encode(hash('sha256', json_encode($requestBody), true));
        
        // Prepare Signature Component
        $componentSignature = "Client-Id:" . $clientId . "\n" . 
                              "Request-Id:" . $requestId . "\n" .
                              "Request-Timestamp:" . $requestDate . "\n" . 
                              "Request-Target:" . $targetPath . "\n" .
                              "Digest:" . $digestValue;
       
        // Calculate HMAC-SHA256 base64 from all the components above
        $signature = base64_encode(hash_hmac('sha256', $componentSignature, $secretKey, true));
        $url = 'https://api-sandbox.doku.com/checkout/v1/payment';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Client-Id:' . $clientId,
            'Request-Id:' . $requestId,
            'Request-Timestamp:' . $requestDate,
            'Signature:' . "HMACSHA256=" . $signature,
        ));
        
        
        $response = curl_exec($ch);
        
        curl_close($ch);
        echo $response;
        echo $componentSignature;
?>
