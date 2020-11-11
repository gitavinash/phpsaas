<?php

    function meta($connection, $data) {

        extract($data);

        $order_id=base64_decode($order_id);

        foreach($data as $order_meta_key => $order_meta_value ){

            $meta_filter=array('order_id', 'type', 'action', 'deliverySlot');

            if(!in_array($order_meta_key, $meta_filter)) {

                mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."order_meta(order_relation_id, order_meta_key, order_meta_value, order_meta_status) VALUES('{$order_id}', '{$order_meta_key}', '{$order_meta_value}', 1)");

            }

            if($order_meta_key=='deliverySlot') {

                $deliverySlot=explode('_', $deliverySlot);
                $deliveryFees=$deliverySlot[1];
                $deliverySlot=$deliverySlot[0];

                mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."order_meta(order_relation_id, order_meta_key, order_meta_value, order_meta_status) VALUES('{$order_id}', 'deliveryFees', '$deliveryFees', 1)");

                mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."order_meta(order_relation_id, order_meta_key, order_meta_value, order_meta_status) VALUES('{$order_id}', 'deliverySlot', '$deliverySlot', 1)");

            }

            $status=200;

        }

        $result = array(
            'status' => ((isset($status)) ? $status : 'error' ),
            'msg' => ((isset($msg)) ? $msg : 'Invalid response' ),
            'redirect' => '/shipping/?id='.base64_encode($order_id)
        );
    
        echo json_encode($result, TRUE);

    }