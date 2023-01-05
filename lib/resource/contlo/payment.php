<?php

    $data = array(
        'event' => 'payment',
        'name' => $profile_display_name,
        'email' => $profile_email,
        'phone_number' => $profile_mobile,
        'properties' => array(
            'amount' => $cart_price,
            'delivery_charge' => $order_delivery_charges,
            'total_amount' => $cart_price+$order_delivery_charges,
            'products' => $productList
        )
    );
    
    // Setup cURL
    $ch = curl_init(CONTLO_API_URL);
    curl_setopt_array($ch, array(
        CURLOPT_POST => TRUE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_HTTPHEADER => array(
            'X-API-KEY: '.CONTLO_API_KEY,
            'Content-Type: application/json'
        ),
        CURLOPT_POSTFIELDS => json_encode($data)
    ));

    // Send the request
    $response = curl_exec($ch);