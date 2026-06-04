<?php

$transaction_id = base64_decode($transaction_id);

switch ($transaction_type) {

    case 'link':

        $validTransactionCheckQuery = "SELECT transaction_id, transaction_order_id as order_id, transaction_cart_id, transaction_method, transaction_payment_mode, transaction_time, transaction_currency, transaction_amount, transaction_type, order_id, order_product_id, order_total_price, order_product_qty, order_product_price, order_delivery_charges, cart_id, cart_relation_id, cart_relation_type FROM " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "cart ON cart_session_id=order_cart_session_id WHERE transaction_id='{$transaction_id}' AND cart_relation_type='link'";
        break;

    case 'enquiry':

        $validTransactionCheckQuery = "SELECT transaction_id, transaction_order_id as order_id, transaction_cart_id, transaction_method, transaction_payment_mode, transaction_time, transaction_currency, transaction_amount, transaction_type, order_id, order_product_id, order_total_price, order_product_qty, order_product_price, order_delivery_charges, cart_id, cart_relation_id, cart_relation_type FROM " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "cart ON cart_session_id=order_cart_session_id WHERE transaction_id='{$transaction_id}' AND cart_relation_type='enquiry'";
        break;

    case 'booking':

        $validTransactionCheckQuery = "SELECT transaction_id, transaction_order_id as order_id, transaction_cart_id, transaction_method, transaction_payment_mode, transaction_time, transaction_currency, transaction_amount, order_id, order_product_id, order_total_price, order_product_qty, order_product_price, order_delivery_charges, cart_id, cart_relation_id, cart_relation_type, booking_id, booking_enquiry_id, booking_date, booking_time, enquiry_id, CONCAT(enquiry_first_name, ' ', enquiry_last_name) as enquiry_display_name, enquiry_email as email, enquiry_mobile as mobile, enquiry_query as guest FROM " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id INNER JOIN " . TABLE_PREFIX . "cart ON cart_id=transaction_cart_id INNER JOIN " . TABLE_PREFIX . "booking ON cart_relation_id=booking_id INNER JOIN " . TABLE_PREFIX . "enquiry ON booking_enquiry_id=enquiry_id WHERE transaction_id='{$transaction_id}' AND cart_relation_type='booking' AND transaction_site_id=" . SITE_ID;
        break;

    case 'visa_application':
        $validTransactionCheckQuery =  "SELECT
                        transaction_method,
                        transaction_amount as order_total_price,
                        transaction_currency,
                        transaction_user_id as userId,
                        order_id,
                        order_cart_session_id,
                        visa_application_id,
                        cart_relation_id,
                        cart_price,
                        cart_currency,
                        profile_mobile,
                        profile_email,
                        profile_display_name,
                        GROUP_CONCAT(CONCAT(visa_country_title, ' - ', visa_package_title) SEPARATOR ', ') AS cartProduct
                    FROM
                        isw_transaction
                    INNER JOIN isw_order ON order_id = transaction_order_id AND transaction_type = order_product_type AND order_site_id = " . SITE_ID . "
                    INNER JOIN isw_cart ON cart_session_id=order_cart_session_id AND cart_site_id=" . SITE_ID . "
                    INNER JOIN isw_profile ON profile_user_id=transaction_user_id
                    INNER JOIN isw_visa_application ON cart_relation_id=visa_application_id AND visa_application_site_id=" . SITE_ID . "
                    INNER JOIN isw_visa_package ON visa_package_id=visa_application_package_id AND visa_package_site_id=" . SITE_ID . "
                    INNER JOIN isw_visa_country ON visa_country_id=visa_package_country_id AND visa_country_site_id=" . SITE_ID . "
                    WHERE
                        transaction_id = {$transaction_id} AND transaction_site_id = " . SITE_ID;;
        break;

    default:
        $validTransactionCheckQuery = "SELECT transaction_id, transaction_order_id as order_id, transaction_cart_id, transaction_method, transaction_payment_mode, transaction_time, transaction_currency, transaction_amount, order_id, order_product_id, order_total_price, order_product_qty, order_product_price, order_delivery_charges, profile_mobile as mobile, profile_email, profile_first_name, profile_last_name, profile_display_name, city_name, state_name, country_name, address_name, address_house_no, address_area, address_landmark, address_pin_code FROM " . TABLE_PREFIX . "transaction INNER JOIN " . TABLE_PREFIX . "profile ON transaction_user_id=profile_user_id INNER JOIN " . TABLE_PREFIX . "order ON transaction_order_id=order_id LEFT JOIN " . TABLE_PREFIX . "address ON address_id=order_address_id LEFT JOIN " . TABLE_PREFIX . "city ON city_id=address_city_id LEFT JOIN " . TABLE_PREFIX . "state ON state_id=address_state_id LEFT JOIN " . TABLE_PREFIX . "country ON country_id=address_country_id WHERE transaction_order_id='{$transaction_id}' AND transaction_site_id=" . SITE_ID;
        break;
}

/** Check transaction details is valid */

if ($checkTransaction = mysqli_query($connection, $validTransactionCheckQuery)) {

    if (mysqli_num_rows($checkTransaction) != 0) {

        extract(mysqli_fetch_assoc($checkTransaction));

        mysqli_query($connection, "START TRANSACTION");

        switch ($transaction_type) {

            case 'link':

                // default thank you page process

                // Update order table

                $updateOrderStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_status=1 WHERE order_id='{$order_id}'");

                // Update cart table

                $updateCartStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_status=1 WHERE cart_id='{$transaction_cart_id}'");

                if ($updateOrderStatus && $updateCartStatus) {

                    mysqli_query($connection, "COMMIT");
                    header("Location: /thank-you/?id={$id}&redirectUrl={$redirectTo}", TRUE, 301);
                    exit();
                } else {

                    echo "Something went wrong";
                    mysqli_query($connection, "ROLLBACK");
                }
                break;

            case 'enquiry':

                // default thank you page process

                // Update order table

                $updateOrderStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_status=1 WHERE order_id='{$order_id}'");

                // Update cart table

                $updateCartStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_status=1 WHERE cart_id='{$transaction_cart_id}'");

                if ($updateOrderStatus && $updateCartStatus) {

                    mysqli_query($connection, "COMMIT");

                    header("Location: /thank-you/?id={$id}&redirectUrl={$redirectTo}", TRUE, 301);
                    exit();
                } else {

                    echo "Something went wrong";
                    mysqli_query($connection, "ROLLBACK");
                }
                break;

            case 'booking':

                // Booking thank you page process

                // Update transaction table

                $updateTransactionStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "transaction SET transaction_status=1 WHERE transaction_id='{$transaction_id}'");

                // Update order table

                $updateOrderStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_status=1 WHERE order_id='{$order_id}'");

                // Update cart table

                $updateCartStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_status=1 WHERE cart_id='{$cart_id}'");

                // Cancel all previous booking request

                $updateBookingStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "booking SET booking_status=5 WHERE booking_date='{$booking_date}' AND booking_time='{$booking_time}'");

                // Update booking table

                $updateBookingStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "booking SET booking_status=1 WHERE booking_id='{$booking_id}'");

                // Update enquiry table

                $updateEnquiryStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "enquiry SET enquiry_status=1 WHERE enquiry_id='{$enquiry_id}'");

                if ($updateTransactionStatus && $updateOrderStatus && $updateCartStatus && $updateBookingStatus && $updateEnquiryStatus) {

                    /** Send SMS */

                    if (file_exists(APP_SMS_TEMPLATE . 'order/booking-confirmation.php')) {
                        require_once APP_SMS_TEMPLATE . 'order/booking-confirmation.php';
                    }

                    /** Send Email */

                    if (file_exists(APP_EMAIL_TEMPLATE . 'order/booking-confirmation.php')) {
                        require_once APP_EMAIL_TEMPLATE . 'order/booking-confirmation.php';
                    }

                    // Send copy email is admin email notification is enable 

                    if (defined('ADMIN_EMAIL_NOTIFICATION')) {

                        if (ADMIN_EMAIL_NOTIFICATION == 1) {

                            // Send booking confirmation email notice to admin

                            include_once ADMIN_EMAIL_TEMPLATE . 'order/booking-confirmation.php';
                            include_once ADMIN_SMS_TEMPLATE . 'order/booking-confirmation.php';
                        }
                    }

                    mysqli_query($connection, "COMMIT");
                } else {

                    $msg = 'Something is wrong while updating the status';
                    mysqli_query($connection, "ROLLBACK");
                }

                break;

            default:

                // default thank you page process

                // Update order table

                $updateOrderStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "order SET order_status=1 WHERE order_id='{$order_id}'");

                // Update cart table

                $updateCartStatus = mysqli_query($connection, "UPDATE " . TABLE_PREFIX . "cart SET cart_status=1 WHERE cart_id='{$transaction_cart_id}'");

                if ($updateOrderStatus && $updateCartStatus) {

                    /** Send SMS */

                    if (file_exists(APP_SMS_TEMPLATE . 'order/prepaid.php')) {
                        require_once APP_SMS_TEMPLATE . 'order/prepaid.php';
                    }

                    /** Send Email */

                    if (file_exists(APP_EMAIL_TEMPLATE . 'order/prepaid.php')) {
                        require_once APP_EMAIL_TEMPLATE . 'order/prepaid.php';
                    }

                    if (file_exists(APP_EMAIL_TEMPLATE . 'order/alert.php')) {
                        require_once APP_EMAIL_TEMPLATE . 'order/alert.php';
                    }

                    mysqli_query($connection, "COMMIT");

                    header("Location: /thank-you/?id={$id}", TRUE, 301);
                    exit();
                } else {

                    echo mysqli_error($connection);
                    mysqli_query($connection, "ROLLBACK");
                }

                break;
        }
    } else {

        echo 'Invalid transaction details found';
        exit();
    }
} else {

    echo mysqli_error($connection);
}
